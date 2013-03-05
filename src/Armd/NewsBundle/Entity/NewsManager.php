<?php

namespace Armd\NewsBundle\Entity;

use Symfony\Component\DependencyInjection\Container;
use Armd\SphinxSearchBundle\Services\Search\SphinxSearch;
use Armd\TagBundle\Entity\TagManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Armd\ListBundle\Entity\ListManager;

class NewsManager extends ListManager
{
    protected $search;

    /** example: true */
    const CRITERIA_FILTRABLE = 'CRITERIA_FILTRABLE';

    /** example: array(13, 7) */
    const CRITERIA_CATEGORY_IDS_OR = 'CRITERIA_CATEGORY_IDS_OR';

    /** example: array('news', 'reportages') */
    const CRITERIA_CATEGORY_SLUGS_OR = 'CRITERIA_CATEGORY_SLUGS_OR';

    /** example: array(13, 7) */
    const CRITERIA_THEME_IDS_OR = 'CRITERIA_THEME_IDS_OR';

    /** example: Date('2012-11-23') */
    const CRITERIA_MEMORIAL_DATE = 'CRITERIA_MEMORIAL_DATE';

    /** example: array('borodino') */
    const CRITERIA_SUBJECT_SLUGS_OR = 'CRITERIA_SUBJECT_SLUGS_OR';

    /** example: true */
    const CRITERIA_IMPORTANT = 'CRITERIA_IMPORTANT';

    /** example: true */
    const CRITERIA_HAS_IMAGE = 'CRITERIA_HAS_IMAGE';

    /** example: Date('2012-01-16') */
    const CRITERIA_NEWS_DATE_SINCE = 'CRITERIA_NEWS_DATE_SINCE';

    /** example: Date('2012-05-16') */
    const CRITERIA_NEWS_DATE_TILL = 'CRITERIA_NEWS_DATE_TILL';

    /** example: Date('2012-05-16') */
    const CRITERIA_NEWS_DATE = 'CRITERIA_NEWS_DATE';

    /** example: array(122, 45) */
    const CRITERIA_NEWS_ID_NOT = 'CRITERIA_NEWS_ID_NOT';

    /** example: true */
    const CRITERIA_IS_ON_MAP = 'CRITERIA_IS_ON_MAP';

    /** example: Date('2012-03-01') */
    const CRITERIA_EVENT_DATE_SINCE = 'CRITERIA_EVENT_DATE_SINCE';

    /** example: Date('2013-03-09') */
    const CRITERIA_EVENT_DATE_TILL = 'CRITERIA_EVENT_DATE_TILL';

    /** example: 'the rolling stones' */
    const CRITERIA_SEARCH_STRING = 'CRITERIA_SEARCH_STRING';

    public function __construct(EntityManager $em, TagManager $tagManager, SphinxSearch $search)
    {
        parent::__construct($em, $tagManager);
        $this->search = $search;
    }

    public function findObjects(array $criteria) {
        if (!empty($criteria[self::CRITERIA_SEARCH_STRING])) {
            return $this->findObjectsWithSphinx($criteria);
        } else {
            return parent::findObjects($criteria);
        }
    }


    public function getQueryBuilder()
    {
        $qb = $this->em->getRepository('ArmdNewsBundle:News')
            ->createQueryBuilder('_news');

        $qb->innerJoin('_news.category', '_newsCategory')
            ->leftJoin('_news.image', '_newsImage', 'WITH', '_newsImage.enabled = true')
            ->andWhere('_news.published = true')
            ->andWhere(
            $qb->expr()->orX(
                $qb->expr()->isNull('_news.publishToDate'),
                $qb->expr()->gte('_news.publishToDate', ':now')
            )
        )
            ->andWhere(
            $qb->expr()->orX(
                $qb->expr()->isNull('_news.publishFromDate'),
                $qb->expr()->lte('_news.publishFromDate', ':now')
            )
        )
            ->orderBy('_news.newsDate', 'DESC')
            ->setParameter('now', new \DateTime());

        return $qb;
    }

    public function setCriteria(QueryBuilder $qb, $criteria)
    {
        parent::setCriteria($qb, $criteria);

        if (!empty($criteria[self::CRITERIA_FILTRABLE])) {
            $qb->andWhere('_newsCategory.filtrable = TRUE');
        }

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_OR])) {
            $qb->andWhere('_news.category IN (:category_ids_or)')
                ->setParameter('category_ids_or', $criteria[self::CRITERIA_CATEGORY_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_CATEGORY_SLUGS_OR])) {
            $qb->andWhere('_newsCategory.slug in (:category_slugs_or)')
                ->setParameter('category_slugs_or', $criteria[self::CRITERIA_CATEGORY_SLUGS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_THEME_IDS_OR])) {
            $qb->andWhere('_news.theme IN (:theme_ids_or)')
                ->setParameter('theme_ids_or', $criteria[self::CRITERIA_THEME_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_MEMORIAL_DATE])) {
            $qb->andWhere('_news.month = :memorial_date_month')
                ->andWhere('_news.day = :memorial_date_day')
                ->setParameter('memorial_date_month', $criteria[self::CRITERIA_MEMORIAL_DATE]->format('m'))
                ->setParameter('memorial_date_day', $criteria[self::CRITERIA_MEMORIAL_DATE]->format('d'));
        }

        if (!empty($criteria[self::CRITERIA_SUBJECT_SLUGS_OR])) {
            $qb->innerJoin('_news.subject', '_newsSubject')
                ->andWhere('_newsSubject.slug IN (:subject_slugs_or)')
                ->setParameter('subject_slugs_or', $criteria[self::CRITERIA_SUBJECT_SLUGS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_IMPORTANT])) {
            $qb->andWhere('_news.important = true');
        }

        if (!empty($criteria[self::CRITERIA_HAS_IMAGE])) {
            $qb->andWhere('_news.image IS NOT NULL');
        }

        if (!empty($criteria[self::CRITERIA_NEWS_DATE_SINCE])) {
            $qb->andWhere('_news.newsDate >= :news_date_since')
                ->setParameter('news_date_since', $criteria[self::CRITERIA_NEWS_DATE_SINCE]->setTime(0, 0));
        }

        if (!empty($criteria[self::CRITERIA_NEWS_DATE_TILL])) {
            $qb->andWhere('_news.newsDate <= :news_date_till')
                ->setParameter('news_date_till', $criteria[self::CRITERIA_NEWS_DATE_TILL]->setTime(23, 59, 59));
        }

        if (!empty($criteria[self::CRITERIA_NEWS_DATE])) {
            $date1 = $criteria[self::CRITERIA_NEWS_DATE];
            $date2 = clone $criteria[self::CRITERIA_NEWS_DATE];
            $date2->modify('+1 day');

            $qb->andWhere("(_news.date <= :news_date1) AND (:news_date2 <= _news.endDate)")
                ->setParameter('news_date1', $date1)
                ->setParameter('news_date2', $date2);
        }

        if (!empty($criteria[self::CRITERIA_EVENT_DATE_SINCE])) {
            $qb->andWhere('_news.date >= :event_date_since OR _news.endDate >= :event_date_since')
                ->setParameter('event_date_since', $criteria[self::CRITERIA_EVENT_DATE_SINCE]->setTime(0, 0));
        }

        if (!empty($criteria[self::CRITERIA_EVENT_DATE_TILL])) {
            $qb->andWhere('_news.date <= :event_date_till OR _news.endDate <= :event_date_till')
                ->setParameter('event_date_till', $criteria[self::CRITERIA_EVENT_DATE_TILL]->setTime(23, 59, 59));
        }

        if (!empty($criteria[self::CRITERIA_NEWS_ID_NOT])) {
            $qb->andWhere('_news.id NOT IN (:news_id_not)')
                ->setParameter('news_id_not', $criteria[self::CRITERIA_NEWS_ID_NOT]);
        }

        if (!empty($criteria[self::CRITERIA_IS_ON_MAP])) {
            $qb->andWhere('_news.isOnMap = TRUE');
        }
    }

    public function findObjectsWithSphinx($criteria) {
        $searchParams = array('News' => array('filters' => array()));

        if (!empty($criteria[self::CRITERIA_LIMIT])) {
            $searchParams['News']['result_limit'] = (int) $criteria[self::CRITERIA_LIMIT];
        }

        if (!empty($criteria[self::CRITERIA_OFFSET])) {
            $searchParams['News']['result_offset'] = (int) $criteria[self::CRITERIA_OFFSET];
        }

        if (!empty($criteria[self::CRITERIA_CATEGORY_SLUGS_OR])) {
            $categories = $this->em->getRepository('ArmdNewsBundle:Category')->findBy(array('slug' => $criteria[self::CRITERIA_CATEGORY_SLUGS_OR]));
            $categoryIds = array();
            foreach ($categories as $category) {
                $categoryIds[] = $category->getId();
            }
            if (!empty($categoryIds)) {
                $searchParams['News']['filters'][] = array(
                    'attribute' => 'category_id',
                    'values' => $categoryIds
                );
            }
        } elseif (!empty($criteria[self::CRITERIA_CATEGORY_IDS_OR])) {
            $searchParams['News']['filters'][] = array(
                'attribute' => 'category_id',
                'values' => $criteria[self::CRITERIA_CATEGORY_IDS_OR]
            );
        }

        $searchResult = $this->search->search($criteria[self::CRITERIA_SEARCH_STRING], $searchParams);

        $result = array();
        if (!empty($searchResult['News']['matches'])) {
            $lectureRepo = $this->em->getRepository('ArmdNewsBundle:News');
            $result = $lectureRepo->findBy(array('id' => array_keys($searchResult['News']['matches'])));
        }

        return $result;
    }


    public function getNewsGroupedByNewsDate($news)
    {
        $newsByDate = array();
        foreach ($news as $n) {
            $date = $n->getNewsDate()->format('Y-m-d');
            if (!isset($newsByDate[$date])) {
                $newsByDate[$date] = array();
            }
            $newsByDate[$date][] = $n;
        }

        return $newsByDate;
    }

    public function getCategories()
    {
        return $this->em->getRepository('ArmdNewsBundle:Category')->findBy(
            array('filtrable' => '1'),
            array('priority' => 'ASC')
        );
    }

    public function getThemes()
    {
        return $this->em->getRepository('ArmdNewsBundle:Theme')->findBy(
            array(),
            array('title' => 'ASC')
        );
    }

    public function filterBy($filter = array())
    {
        $qb = $this->em->getRepository($this->class)->createQueryBuilder('n');
        $qb->select('n, c, t, i')
            ->innerJoin('n.category', 'c')
            ->innerJoin('n.theme', 't')
            ->leftJoin('n.image', 'i', 'WITH', 'i.enabled = true')
            ->andWhere('n.published = true');

        // имеющие геопривязку
        if (isset($filter['is_on_map'])) {
            $qb->andWhere('n.isOnMap = TRUE');
        }

        // фильтр по выбранным категориям
        if (isset($filter['category'])) {
            $categoryIds = (array)$filter['category'];
            $qb->andWhere('c.id IN (:categoryIds)')
               ->setParameter(':categoryIds', $categoryIds);
        }

        // фильтр по выбранным тематикам (иконкам)
        if (isset($filter['theme'])) {
            $themeIds = (array)$filter['theme'];
            $qb->andWhere('t.id IN (:themeIds)')
               ->setParameter(':themeIds', $themeIds);
        }

        // фильтр по датам
        if (isset($filter['date_from']) && isset($filter['date_to'])) {
            $dateFrom = isset($filter['date_from']) ? new \DateTime($filter['date_from']) : new \DateTime('now');
            $dateTo = isset($filter['date_to']) ? new \DateTime($filter['date_to']) : new \DateTime('now');
            $qb->andWhere('(n.date >= (:dateFrom) AND n.date <= (:dateTo)) OR (n.endDate >= (:dateFrom) AND n.endDate <= (:dateTo))')
               ->setParameter(':dateFrom', $dateFrom)
               ->setParameter(':dateTo', $dateTo);
        }

        // result
        $rows = $qb->getQuery()->getResult();

        $data = array();
        foreach ($rows as $row) {
            $imageUrl = $this->container->get('sonata.media.twig.extension')->path($row->getImage(), 'thumbnail');
            $iconUrl = $this->container->get('sonata.media.twig.extension')->path($row->getTheme()->getIconMedia(), 'reference');
            $data[] = array(
                'id' => $row->getId(),
                'title' => $row->getTitle(),
                //'dateFrom' => $row->getDate(),
                //'dateTo' => $row->getEndDate(),
                'lon' => $row->getLon(),
                'lat' => $row->getLat(),
                'imageUrl' => $imageUrl,
                'iconUrl' => $iconUrl,
                'categoryId' => $row->getCategory()->getId(),
                'themeId' => $row->getTheme()->getId(),
            );
        }

        return $data;
    }

    public function getLastNews($limit = 5)
    {
        $qb = $this->getQueryBuilder(array());
        $qb->orderBy('n.date', 'DESC');

        return $qb->getQuery()
            ->setMaxResults($limit)
            ->getResult();
    }

    public function updateImageDescription($news)
    {
        if ($news->getImage() && !$news->getImage()->getDescription()) {
            $news->getImage()->setDescription($news->getTitle());
        }
    }


    public function getClassName()
    {
        return 'Armd\NewsBundle\Entity\News';
    }

    public function getTaggableType()
    {
        return 'armd_news';
    }
}
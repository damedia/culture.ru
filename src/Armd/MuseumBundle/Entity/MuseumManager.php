<?php

namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\EntityManager;
use Armd\TagBundle\Entity\TagManager;
use Doctrine\ORM\QueryBuilder;
use Armd\SphinxSearchBundle\Services\Search\SphinxSearch;
use Knp\Component\Pager\Paginator;
use Armd\ListBundle\Entity\ListManager;

class MuseumManager extends ListManager
{
    protected $search;

    /** example: array(13, 7) */
    const CRITERIA_REGION_IDS_OR = 'CRITERIA_REGION_IDS_OR';

    /** example: array(13, 7) */
    const CRITERIA_CATEGORY_IDS_OR = 'CRITERIA_CATEGORY_IDS_OR';

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
        $qb = $this->em->getRepository('ArmdMuseumBundle:Museum')
            ->createQueryBuilder('_museum');

        $qb->leftJoin('_museum.image', '_museumImage', 'WITH', '_museumImage.enabled = TRUE')
            ->andWhere('_museum.published = TRUE')
        ;

        return $qb;
    }

    public function setCriteria(QueryBuilder $qb, $criteria)
    {
        parent::setCriteria($qb, $criteria);

        if (!empty($criteria[self::CRITERIA_REGION_IDS_OR])) {
            $qb->andWhere('_museum.region IN (:region_ids_or)')
                ->setParameter('region_ids_or', $criteria[self::CRITERIA_REGION_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_OR])) {
            $qb->innerJoin('_museum.category', '_museumCategory')
                ->andWhere('_museum.category IN (:category_ids_or)')
                ->setParameter('category_ids_or', $criteria[self::CRITERIA_CATEGORY_IDS_OR]);
        }
    }

    public function findObjectsWithSphinx(array $criteria)
    {
        $searchParams = array('Museums' => array(
            'filters' => array(
                array(
                    'attribute' => 'published',
                    'values' => array(1)
                )
            )
        ));

        if (isset($criteria[self::CRITERIA_LIMIT])) {
            $searchParams['Museums']['result_limit'] = (int) $criteria[self::CRITERIA_LIMIT];
        }

        if (isset($criteria[self::CRITERIA_OFFSET])) {
            $searchParams['Museums']['result_offset'] = (int) $criteria[self::CRITERIA_OFFSET];
        }

        $searchResult = $this->search->search($criteria[self::CRITERIA_SEARCH_STRING], $searchParams);

        $result = array();
        if (!empty($searchResult['Museums']['matches'])) {
            $lectureRepo = $this->em->getRepository('ArmdMuseumBundle:Museum');
            $result = $lectureRepo->findBy(array('id' => array_keys($searchResult['Museums']['matches'])));
        }

        return $result;

    }

    public function getCategories()
    {
        return $this->em->getRepository('ArmdMuseumBundle:Category')->findBy(
            array(),
            array('title' => 'ASC')
        );
    }

    public function getDistinctRegions()
    {
        $query = $this->em->createQuery("
            SELECT DISTINCT region.id, region.title
            FROM ArmdMuseumBundle:Museum museum
            JOIN ArmdAtlasBundle:Region region
            WITH region.id = museum.region
            ORDER BY region.title
        ");

        return $query->getResult();
    }

    public function getClassName()
    {
        return 'Armd\MuseumBundle\Entity\Museum';
    }

    public function getTaggableType()
    {
        return 'armd_museum';
    }

}

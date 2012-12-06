<?php

namespace Armd\NewsBundle\Entity;

use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;

class NewsManager
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string                      $class
     */
    public function __construct(EntityManager $em, $class, $container)
    {
        $this->em = $em;
        $this->class = $class;
        $this->container = $container;
    }
    
    public function getPager(array $criteria, $page, $limit = 10)
    {
        return $this->getQueryBuilder($criteria)
            ->getQuery()
            ->setMaxResults($limit)
            ->getResult()
        ;
    }
        
    public function getQueryBuilder(array $criteria)
    {
        $qb = $this->em->getRepository($this->class)->createQueryBuilder('n');
        $qb->select('n, c, i')
            ->innerJoin('n.category', 'c')
            ->leftJoin('n.image', 'i', 'WITH', 'i.enabled = true')
            ->andWhere('n.published = true')
            ->andWhere($qb->expr()->orX(
                    $qb->expr()->isNull('n.publishToDate'),
                    $qb->expr()->gte('n.publishToDate', ':now')
                )
            )
            ->andWhere($qb->expr()->orX(
                    $qb->expr()->isNull('n.publishFromDate'),
                    $qb->expr()->lte('n.publishFromDate', ':now')
                )
            )
            ->orderBy('n.newsDate', 'DESC')
            ->setParameter('now', new \DateTime())
        ;
        
        $this->setCriteria($qb, $criteria);
        
        return $qb;        
    }
    
    protected function setCriteria($qb, array $criteria)
    {   
        if (!empty($criteria['category'])) {
            $qb
                ->andWhere('c.slug in (:slugs)')
                ->setParameter(':slugs', (array)$criteria['category'])
            ;
        } else {
            $qb
                ->andWhere('c.filtrable = true')
            ;
        }

        if (!empty($criteria['memorial_date'])) {        
            $qb
                ->andWhere('n.month = :month')
                ->andWhere('n.day = :day')            
                ->setParameter('month', $criteria['memorial_date']->format('m'))
                ->setParameter('day', $criteria['memorial_date']->format('d'))
            ;
        }                 
            
        if (!empty($criteria['subject'])) {
            $qb
                ->innerJoin('n.subject', 's', 'WITH', 's.slug = :slug')
                ->setParameter('slug', $criteria['subject'])
            ;
        }
        
        if (!empty($criteria['important'])) {
            $qb
                ->andWhere('n.important = true')
            ;
        }

        if (!empty($criteria['has_image'])) {
            $qb
                ->andWhere('i IS NOT NULL')
            ;
        }

        if (!empty($criteria['from_date'])) {
            $qb->andWhere($qb->expr()->gt('n.date', ':from_date'))
               ->setParameter('from_date', new \DateTime($criteria['from_date'] . '00:00:00'));
        }

        if (!empty($criteria['to_date'])) {
            $qb->andWhere($qb->expr()->lt('n.date', ':to_date'))
               ->setParameter('to_date', new \DateTime($criteria['to_date'] . ' 23:59:59'))
            ;
        }

        if (!empty($criteria['target_date'])) {
            $targetDateFrom = $criteria['target_date'];
            $targetDateTo   = clone $criteria['target_date'];
            $targetDateTo->modify('+1 day');

            $qb->andWhere("(n.date <= :target_date_to) AND (:target_date_from <= n.endDate)")
               ->setParameter('target_date_from', $targetDateFrom)
               ->setParameter('target_date_to', $targetDateTo)
            ;
        }

        if (!empty($criteria['order_by_publish_date'])) {
            $qb->orderBy('n.publishedAt', 'DESC');
        }

    }
    
    public function getCategories()
    {
        return $this->em->getRepository('ArmdNewsBundle:Category')->findBy(array('filtrable' => '1'), array('priority' => 'ASC'));
    }

    public function filterBy($filter=array())
    {
        $qb = $this->em->getRepository($this->class)->createQueryBuilder('n');
        $qb->select('n, c, i')
           ->innerJoin('n.category', 'c')
           ->leftJoin('n.image', 'i', 'WITH', 'i.enabled = true')
           ->andWhere('n.published = true');

        // имеющие геопривязку
        if (isset($filter['is_on_map'])) {
            $qb->andWhere('n.isOnMap = TRUE');
        }

        // фильтр по выбранным категориям
        if (isset($filter['category'])) {
            $categoryIds = (array) $filter['category'];
            $qb->andWhere('c.id IN (:categoryIds)')
               ->setParameter(':categoryIds', $categoryIds);
        } else {
            throw new \Exception('Выберите хотя бы один тип события.');
        }

        // фильтр по датам
        $dateFrom = isset($filter['date_from']) ? new \DateTime($filter['date_from']) : new \DateTime('now');
        $dateTo   = isset($filter['date_to'])   ? new \DateTime($filter['date_to'])   : new \DateTime('now');
        $qb->andWhere('(n.date >= (:dateFrom) AND n.date <= (:dateTo)) OR (n.endDate >= (:dateFrom) AND n.endDate <= (:dateTo))')
           ->setParameter(':dateFrom', $dateFrom)
           ->setParameter(':dateTo', $dateTo);

        // result
        $rows = $qb->getQuery()->getResult();

        $data = array();
        foreach ($rows as $row) {
            $imageUrl = $this->container->get('sonata.media.twig.extension')->path($row->getImage(), 'thumbnail');
            $data[] = array(
                'id' => $row->getId(),
                'title' => $row->getTitle(),
                //'dateFrom' => $row->getDate(),
                //'dateTo' => $row->getEndDate(),
                'lon' => $row->getLon(),
                'lat' => $row->getLat(),
                'imageUrl' => $imageUrl,
                'categoryId' => $row->getCategory()->getId(),
            );
        }
        return $data;
    }

    public function getLastNews($limit=5)
    {
        $qb = $this->getQueryBuilder(array());
        $qb->orderBy('n.date', 'DESC');
        return $qb->getQuery()
                  ->setMaxResults($limit)
                  ->getResult();
    }

    public function getBillboardNews()
    {
        $entities = array();
        foreach ($this->getCategories() as $category) {
            //var_dump($category);
            $entity = $this->em->getRepository('ArmdNewsBundle:News')->findOneBy(
                array('category' => $category, 'important' => true),
                array('date' => 'DESC')
            );
            if (! $entity) {
                $entity = $this->em->getRepository('ArmdNewsBundle:News')->findOneBy(
                    array('category' => $category),
                    array('date' => 'DESC')
                );
            }
            $entities[] = $entity;
        }
        return $entities;
    }

    public function getSiblingNews($entity, $limit=10)
    {
        $criteria = array('category'=>$entity->getCategory()->getSlug());
        $qb = $this->getQueryBuilder($criteria);
        $qb->orderBy('n.date', 'DESC');
        $rows = $qb->getQuery()
            ->setMaxResults($limit+1)
            ->getResult();

        $res = array();
        foreach ($rows as $i=>$row) {
            if ($row->getId() != $entity->getId())
                $res[] = $row;
        }
        $res = array_slice($res, 0, $limit);
        return $res;
    }

    public function updateImageDescription($news)
    {
        if ($news->getImage() && !$news->getImage()->getDescription()) {
            $news->getImage()->setDescription($news->getTitle());
        }
    }


}
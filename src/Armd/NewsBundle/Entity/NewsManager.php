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
                $qb->expr()->isNull('n.date'),
                $qb->expr()->lte('n.date', ':now')
            ))
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('n.endDate'),
                $qb->expr()->gt('n.endDate', ':now')
            ))
            ->orderBy('n.date', 'DESC')
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

    }
    
    public function getCategories()
    {
        return $this->em->getRepository('ArmdNewsBundle:Category')->findBy(array('filtrable' => '1'), array('priority' => 'ASC'));
    }

    public function filterBy($filter=array())
    {
        $qb = $this->getQueryBuilder(array());

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

}
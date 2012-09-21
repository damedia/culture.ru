<?php

namespace Armd\NewsBundle\Entity;

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
     * @param \Doctrine\ORM\EntityManager $em
     * @param string                      $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
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
        $qb = $this->em->getRepository($this->class)->createQueryBuilder('n')
            ->select('n, c, i')
            ->innerJoin('n.category', 'c')
            ->leftJoin('n.image', 'i', 'WITH', 'i.enabled = true')
            ->andWhere('n.published = true')
            ->orderBy('n.date', 'DESC')
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
            
        if (!empty($criteria['borodino'])) {
            $qb
                ->andWhere('n.borodino = true')
            ;
        }
        
        if (!empty($criteria['important'])) {
            $qb
                ->andWhere('n.important = true')
            ;
        }        
    }
    
    public function getCategories()
    {
        return $this->em->getRepository('ArmdNewsBundle:Category')->findBy(array('filtrable' => '1'), array('priority' => 'ASC'));
    }                    
}

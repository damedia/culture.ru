<?php

namespace Armd\EventBundle\Entity;

use Doctrine\ORM\EntityManager;

class EventManager
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
        return $this->getQueryBuilder($criteria)->getQuery()
            ->getResult();
    }
    
    public function getQueryBuilder($criteria)
    {
        $parameters = array();
        $query = $this->em->getRepository($this->class)->createQueryBuilder('e')
            ->addSelect('r')
            ->innerJoin('e.region', 'r')
            ->andWhere('e.published = true')
            ->orderBy('e.beginDate')
        ;
        
        if (isset($criteria['region_id']) && intval($criteria['region_id'])) {
            $query->andWhere('e.region = :region_id');
            $parameters['region_id'] = $criteria['region_id'];
        }
        
        if (isset($criteria['month']) && $criteria['month'] > 0) {
            $query->andWhere('e.beginDate >= :from and e.beginDate < :to');
//            $query->andWhere('e.beginDate >= :from and e.beginDate < :to');
//            $query->andWhere('e.beginDate >= :from or e.endDate is null');
            $parameters['from'] = \DateTime::createFromFormat('m.d H:i:s', sprintf("%02d.01 00:00:00", $criteria['month']));
            $parameters['to'] = \DateTime::createFromFormat('m.d H:i:s', sprintf("%02d.01 00:00:00", $criteria['month'] + 1));            
        }
    
        $query->setParameters($parameters);
        
        return $query;
    }
    
    public function getDistinctMonths()
    {
        $result = array();
        $query = $this->em->getRepository($this->class)->createQueryBuilder('e')
            ->select('e.beginDate')
            ->distinct()
            ->andWhere('e.published = true')
        ;
        
        $dates = $query->getQuery()->getArrayResult();
        
        foreach($dates as $d)
        {
            $date = $d['beginDate'];
            $result[$date->format('m')] = $date->format('M');
        }

        ksort($result);
        
        return $result;
    }
    
    public function getDistinctRegions()
    {
        $result = array();
        $query = $this->em->getRepository('Armd\AtlasBundle\Entity\Region')->createQueryBuilder('r')
            ->distinct()
            ->from($this->class, 'e')
            ->andWhere('e.region = r.id')
            ->andWhere('e.published = true')            
            ->orderBy('r.sortIndex')
            ->addOrderBy('r.title')
        ;
        
        $regions = $query->getQuery()->getResult();

        foreach($regions as $r) {
            $result[$r->getId()] = $r->getTitle();
        }

        return $result;                
    }            
}

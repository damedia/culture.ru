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
        return $this->qetQueryBuilder($criteria)->getQuery()
            ->getResult();
    }
    
    public function qetQueryBuilder($criteria)
    {
        $parameters = array();
        $query = $this->em->getRepository($this->class)->createQueryBuilder('e')
            ->select('e', 'r')
            ->innerJoin('e.region', 'r')
            ->andWhere('e.published = true')
            ->orderBy('e.beginDate')
        ;
        
        if (isset($criteria['region_id'])) {
            $query->andWhere('e.region = :region_id');
            $parameters['region_id'] = $criteria['region_id'];
        }
        
        if (isset($criteria['month'])) {
            $query->andWhere('e.beginDate between :from and :to');
//            $query->andWhere('e.beginDate >= :from or e.endDate is null');
            $parameters['from'] = \DateTime::createFromFormat('m.d H:i:s', sprintf("%02d.01 00:00:00", $criteria['month']));
            $parameters['to'] = \DateTime::createFromFormat('m.d H:i:s', sprintf("%02d.01 00:00:00", $criteria['month'] + 1));            
        }
    
        $query->setParameters($parameters);
        
        return $query;
    }            
}

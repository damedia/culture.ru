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
    
    public function getList()
    {
        $query = $this->em->getRepository($this->class)->createQueryBuilder('e')
            ->select('e', 'r')
            ->innerJoin('e.region', 'r')
            ->andWhere('e.published = true')
            ->orderBy('e.beginDate')
            ->getQuery()
        ;
        
        return $query->getResult();
    }            
}

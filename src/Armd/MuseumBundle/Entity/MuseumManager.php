<?php

namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\EntityManager;

class MuseumManager
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
        $query = $this->em->getRepository($this->class)->createQueryBuilder('m')
            ->addSelect('m, i')
            ->leftJoin('m.image', 'i', 'WITH', 'i.enabled = true')
            ->andWhere('m.published = true')
            ->orderBy('m.title')
        ;
                
        return $query;
    }
}

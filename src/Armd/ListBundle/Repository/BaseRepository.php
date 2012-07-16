<?php

namespace Armd\ListBundle\Repository;

use Doctrine\ORM\EntityRepository;

class BaseRepository extends EntityRepository
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $qb;

    /**
     * @var string
     */    
    protected $alias;
    
    /**
     * {@inheritdoc}
     */
    function createQueryBuilder($alias)
    {
        $this->qb = parent::createQueryBuilder($alias);
        $this->alias = $alias;
        
        return $this->qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */        
    function getQueryBuilder()
    {
        return $this->qb;
    }

    /**
     * @return \Doctrine\ORM\Query
     */            
    function getQuery()
    {
        return $this->qb->getQuery();
    }    
}
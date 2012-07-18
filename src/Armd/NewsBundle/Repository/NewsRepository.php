<?php

namespace Armd\NewsBundle\Repository;

use Armd\ListBundle\Repository\ListRepository;

class NewsRepository extends ListRepository
{    
    /**
     * @param \DateTime $date
     * @return NewsRepository
     */
    function setBeginDate(\DateTime $date)
    {
        $this->qb
            ->andWhere("{$this->alias}.date >= :date")
            ->setParameter('date', $date)
        ;
        
        return $this;
    }
    
    /**
     * @param \DateTime $date
     * @return NewsRepository     
     */    
    function setEndDate(\DateTime $date)
    {
        $this->qb
            ->andWhere("{$this->alias}.date <= :date")
            ->setParameter('date', $date)
        ;
        
        return $this;        
    }
    
    /**
     * @param \DateTime $date
     * @return NewsRepository     
     */    
    function setContext($context)
    {
        if (!$context)
        {
            return $this;
        }
        
        return $this;        
    }
    

    /**
     * @param string $order
     * @return NewsRepository     
     */        
    function orderByDate($order = 'desc')
    {
        $this->qb->orderBy("{$this->alias}.date", $order);
        
        return $this;
    }
}

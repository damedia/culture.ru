<?php

namespace Armd\ExhibitBundle\Repository;

use Armd\ListBundle\Repository\BaseRepository;

class ArtObjectRepository extends BaseRepository
{   
    function setLimit($limit = 0, $offset = 0)
    {
        if ($limit) {
            $this->qb->setMaxResults($limit);
            
            if ($offset) {
                $this->qb->setFirstResult($offset);
            }
        }
        
        return $this;
    }
    
    function orderByCentury($order = 'DESC')
    {
        $this->qb->orderBy("{$this->alias}.century", $order);
            
        return $this;
    }
    
    function orderByDate($order = 'ASC')
    {
        $this->qb->orderBy("{$this->alias}.date", $order);
            
        return $this;
    }    
}

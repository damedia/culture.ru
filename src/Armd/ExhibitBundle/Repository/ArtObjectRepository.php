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
    
    function setPublished()
    {
        $this->qb->andWhere("{$this->alias}.published = :published")->setParameter('published', true);
            
        return $this;
    }
    
    public function selectCount()
    {
        $this->qb->select("COUNT({$this->alias}.id)");                    
        
        return $this;
    }
}

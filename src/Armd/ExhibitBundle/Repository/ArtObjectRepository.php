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
    
    function setDistinct()
    {
        $this->qb->select("DISTINCT {$this->alias}");
            
        return $this;
    }
    
    function setAuthors($authors = array())
    {
        $this->qb
            ->join("{$this->alias}.authors", 'a')
            ->andWhere('a.id IN (:authors)')->setParameter('authors', $authors);
            
        return $this;
    }
    
    function setMuseums($museums = array())
    {
        $this->qb
            ->andWhere("{$this->alias}.museum IN (:museums)")->setParameter('museums', $museums);
            
        return $this;
    }
    
    function setCategories($categories = array())
    {
        $this->qb
            ->join("{$this->alias}.categories", 'c')
            ->andWhere('c.id IN (:categories)')->setParameter('categories', $categories);
            
        return $this;
    }
    
    function orderByDate($order = 'ASC')
    {
        $this->qb
            ->orderBy("{$this->alias}.date", $order)
            ->addOrderBy("{$this->alias}.id", 'ASC');
            
        return $this;
    }
    
    public function selectCount()
    {
        $this->qb->select("COUNT({$this->alias}.id)");                    
        
        return $this;
    }
}

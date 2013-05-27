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
    
    function setSearch($text)
    {
        $this->qb
            ->leftJoin("{$this->alias}.authors", 'a')
            ->andWhere("LOWER({$this->alias}.title) LIKE LOWER(:text)")->setParameter('text', '%' . $text . '%')
            ->orWhere("LOWER(a.name) LIKE LOWER(:text)")->setParameter('text', '%' . $text . '%');
            
        return $this;
    }
    
    function orderByDate($order = 'ASC')
    {
        $this->qb
            ->addOrderBy("{$this->alias}.date", $order)
            ->addOrderBy("{$this->alias}.id", 'ASC');
            
        return $this;
    }
    
    function setNotId($id)
    {
        $this->qb
            ->andWhere("{$this->alias}.id != :id")->setParameter('id', $id);
            
        return $this;
    }
    
    public function getCount()
    {
        return $this->qb
            ->select("COUNT(DISTINCT {$this->alias}.id)")
            ->getQuery()
            ->getSingleScalarResult();      
    }
}

<?php

namespace Armd\ExtendedNewsBundle\Repository;

use Armd\NewsBundle\Repository\NewsRepository as BaseRepository;

class NewsRepository extends BaseRepository
{
    /**
     * return NewsRepository
     */    
    function addImage()
    {
        $this->qb
            ->addSelect('i')
            ->leftJoin("{$this->alias}.image", 'i')
        ;
        
        return $this; 
    }
}

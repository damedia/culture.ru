<?php

namespace Armd\ListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller
{
    /**
     * @return \Doctrine\ORM\EntityRepository
     */    
    protected function getEntityRepository($alias = 't')
    {           
        $repository = $this->getDoctrine()->getEntityManager()->getRepository($this->getEntityName());
        $repository->createQueryBuilder($alias);
        
        return $repository;
    }
    
//    /**
//     * @param \Doctrine\ORM\Query $query
//     * @param int $page
//     * @param int $limit
//     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
//     */
//    public function getPagination(\Doctrine\ORM\Query $query, $page, $limit)
//    {
//        return $this->get('knp_paginator')->paginate($query, $page, $limit);
//    }

    /**
     * @return string
     */        
    function getEntityName()
    {
        return $this->getControllerName();
    }        
    
    /**
     * @return string
     */                
    function getTemplateName($action)
    {
        return "{$this->getControllerName()}:{$action}.html.twig";
    }
    
    /**
     * @return string
     */            
    abstract function getControllerName();
}

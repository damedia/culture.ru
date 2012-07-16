<?php

namespace Armd\ListBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ListController extends Controller
{
    /**
     * @param integer $page
     * @Route("/", defaults={"page" = 1})     
     * @Route("/page/{page}", requirements={"page" = "\d+"})
     */
    function listAction($page, $limit = 10)
    {
        return $this->render($this->getTemplateName('list'), array('entities' => $this->getPagination($this->getListRepository()->getQuery(), $page, $limit)));
    }

    /**
     * @param integer $id
     * @Route("/{id}", requirements={"id" = "\d+"})     
     */
    function itemAction($id)
    {
        $entity = $this->getEntityRepository()->find($id);

        if (null === $entity) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $id));
        }

        return $this->render($this->getTemplateName('item'), array('entity' => $entity));
    }

    /**
     * @return Armd\ListBundle\Repository\ListRepository
     */
    function getListRepository()
    {
        $repository = $this->getEntityRepository();

        return $repository;
    }
    
    /**
     * @return \Doctrine\ORM\EntityRepository
     */    
    protected function getEntityRepository($alias = 't')
    {           
        $repository = $this->getDoctrine()->getEntityManager()->getRepository($this->getEntityName());
        $repository->createQueryBuilder($alias);
        
        return $repository;
    }
    
    /**
     * @param \Doctrine\ORM\Query $query
     * @param int $page
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    public function getPagination(\Doctrine\ORM\Query $query, $page, $limit)
    {   
        return $this->get('knp_paginator')->paginate($query, $page, $limit);
    }

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
    function getControllerName()
    {
        return 'ArmdListBundle:List';
    }    
}

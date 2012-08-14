<?php

namespace Armd\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\ListBundle\Controller\ListController;
use Armd\ListBundle\Repository\BaseRepository;

class MainController extends Controller
{
    /**
     * @Route("/", name="armd_news_list_index")     
     * @Route("/page/{page}", defaults={"page" = 1}, name="armd_news_list_index_by_page")     
     */
    public function indexAction()
    {
        //$categories = array('news');
        return $this->render('ArmdMainBundle:Main:index.html.twig', array(
            'news_entities' => $this->getImportantNewsList(array('news')), 
            'events_entities' => $this->getImportantNewsList(array('events')),
            'interviews_entities' => $this->getImportantNewsList(array('interviews'))
        ));   
    }
    
    function getImportantNewsList(array $categories = array(), $limit = 3)
    {        
        $repository = $this->getListRepository()
            ->setImportant(true)
            ->orderByPriority()
        ;
        
        if ($categories) {
            $repository->setCategories($categories);
        } else {
            $repository->setFiltrableCategories();
        }
        
        $query = $repository->getQuery();
        
        if ($limit)
        {
            $query->setMaxResults($limit);
        }
        
        return $query->getResult();
    }
 
    function getListRepository()
    {
        //return $this->getEntityRepository();
        return $this->getEntityRepository()
            ->setPublication()
            ->orderByDate()
        ;
    }
    
    protected function getEntityRepository($alias = 't')
    {           
        $repository = $this->getDoctrine()->getEntityManager()->getRepository($this->getEntityName());
        $repository->createQueryBuilder($alias);
        
        return $repository;
    }
    
    function getEntityName()
    {
        return $this->getControllerName();
    }  
    
    function getControllerName()
    {
        return 'ArmdNewsBundle:News';
    }        
}

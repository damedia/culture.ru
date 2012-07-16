<?php

namespace Armd\NewsBundle\Controller;

use Armd\ListBundle\Controller\ListController;

class NewsController extends ListController
{
    /**
     * @param string $from
     * @param string $to
     * @param int    $page
     */
    function archiveAction($from, $to, $page)
    {
        return $this->render($this->getTemplateName('archive'), array('entities' => $this->getPagination($this->getArchiveRepository($from, $to)->getQuery(), $page)));
    }

    /**
     * @param string $from
     * @param string $to
     * @return \Armd\NewsBundle\Repository\NewsRepository
     */    
    function getArchiveRepository($from, $to)
    {   
        $format = 'd.m.Y';
        $date = $to ? \DateTime::createFromFormat($format, $to) : new \DateTime();
     
        $repository = parent::getListRepository()->setEndDate($date);           
        
        return $from ? $repository->setBeginDate(\DateTime::createFromFormat($format, $from)) : $repository;        
    }

    /**
     * {@inheritdoc}
     */
    function getListRepository()
    {
        return parent::getListRepository()
            ->setEndDate(new \DateTime())
            ->orderByDate()
        ;
    }
    
    function getControllerName()
    {
        return 'ArmdNewsBundle:News';
    }
}

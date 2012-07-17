<?php

namespace Armd\NewsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Armd\ListBundle\Controller\ListController;

class NewsController extends ListController
{
    /**
     * @Route("/calendar/", defaults={"year" = null, "month" = null, "day" = null})     
     * @Route("/calendar/{year}/{month}/{day}", requirements={"year" = "\d{4}", "month" = "\d{2}", "day" = "\d{2}"})
     */
    function archiveAction($year, $month, $day)
    {
        $date = $this->getDate($year, $month, $day);
        
        return $this->render($this->getTemplateName('archive'), array(
            'billboard'     => $this->getPagination($this->getArchiveRepository($date, true)->getQuery(), 1, 100),
            'entities'      => $this->getPagination($this->getArchiveRepository($date)->getQuery(), 1, 100),            
            'date'          => $date,
        ));
    }
    
    /**
     * @Route("/this-day", defaults={"month" = null, "day" = null})
     * @Route("/this-day/{month}/{day}", requirements={"month" = "\d{2}", "day" = "\d{2}"})          
     */        
    function thisDayListAction($month, $day)
    {
        return $this->render($this->getTemplateName('today-list'), array('entities' => $this->getThisDayListRepository($month, $day)));
    }
    
    
    function getThisDayListRepository($month, $day)
    {
        return parent::getListRepository();
    }
    
    /**
     * @param string $from
     * @param string $to
     * @return \Armd\NewsBundle\Repository\NewsRepository
     */    
    function getArchiveRepository($date)
    {   
        $format = 'd.m.Y';
        $date = $to ? \DateTime::createFromFormat($format, $to) : new \DateTime();
     
        $repository = parent::getListRepository()
            ->setEndDate($date)
            ->orderByDate();           
        
        return $from ? $repository->setBeginDate(\DateTime::createFromFormat($format, $from)) : $repository;        
    }

    /**
     * {@inheritdoc}
     */
    function getListRepository($date)
    {
        return parent::getListRepository()

        ;
    }
    
    function getDate($date)
    {
        $format = 'd.m.Y';
        return $date ? \DateTime::createFromFormat($format, $date) : new \DateTime();
    }
    
    function getControllerName()
    {
        return 'ArmdNewsBundle:News';
    }
}

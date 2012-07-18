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
            'billboard'     => $this->getPagination($this->getArchiveRepository($date, $date, 'important')->getQuery(), 1, 100),
            'entities'      => $this->getPagination($this->getArchiveRepository($date, $date, 'unimportant')->getQuery(), 1, 100),
//            'today'         => $this->getPagination($this->getThisDayListRepository($date)->getQuery(), 1, 100),            
            'date'          => $date,
        ));
    }
    
    function thisDayListAction($month, $day)
    {
        return $this->render($this->getTemplateName('today-list'), array('entities' => $this->getThisDayListRepository($month, $day)));
    }
    
    function getThisDayListRepository($month, $day)
    {
        return $this->getListRepository()
/*
            ->setMonth($month)
            ->setDay($day)
*/
        ;
    }
    
    /**
     * @param string \DateTime
     * @param string \DateTime
     * @return \Armd\NewsBundle\Repository\NewsRepository
     */    
    function getArchiveRepository($from, $to, $context = null)
    {
        $repository = $this->getListRepository()
            ->setBeginDate($from->setTime(0, 0, 0))
            ->setEndDate($to->setTime(23, 59, 59))
            ->setContext($context)
        ;
        
        return $repository;        
    }

    /**
     * {@inheritdoc}
     */
    function getListRepository()
    {
        return parent::getListRepository()
            ->orderByDate();
        ;
    }
    
    function getDate($year, $month, $day)
    {
        $format = 'Ymd';
        $date = "{$year}{$month}{$day}";

        return $date ? \DateTime::createFromFormat($format, $date) : new \DateTime();
    }
    
    function getControllerName()
    {
        return 'ArmdNewsBundle:News';
    }
}

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
        $begin = clone($date);
        $begin->sub(new \DateInterval('P14D'));
        
        return $this->render($this->getTemplateName('archive'), array(
            'billboard'     => $this->getPagination($this->getArchiveRepository($begin, $date, true)->getQuery(), 1, 100),
            'entities'      => $this->getPagination($this->getArchiveRepository($begin, $date)->getQuery(), 1, 100),
            'today'         => $this->getPagination($this->getThisDayListRepository($date->format('m'), $date->format('d'))->getQuery(), 1, 100),            
            'date'          => $date,
        ));
    }
    
    
    function thisDayListAction($month, $day)
    {
        return $this->render($this->getTemplateName('today-list'), array('entities' => $this->getThisDayListRepository($month, $day)));
    }
    
    function getImportantNewsList(array $categories = array(), $limit = 0)
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
        
        return $repository->getQuery()->getResult();
    }
    
    function getThisDayListRepository($month, $day)
    {
        return $this->getListRepository()
/*
            ->setMonth($month)
            ->setDay($day)        
*/
            ->setCategories(array(1))                    
        ;
    }
    
    /**
     * @param string \DateTime
     * @param string \DateTime
     * @return \Armd\NewsBundle\Repository\NewsRepository
     */    
    function getArchiveRepository($from, $to, $isImportant = false)
    {
        $repository = $this->getListRepository()
            ->setBeginDate($from->setTime(0, 0, 0))
            ->setEndDate($to->setTime(23, 59, 59))
            ->setFiltrableCategories()       
            ->setImportant($isImportant)
        ;
        
        return $repository;        
    }

    /**
     * {@inheritdoc}
     */
    function getListRepository()
    {
        return parent::getListRepository()
            ->setPublication()
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

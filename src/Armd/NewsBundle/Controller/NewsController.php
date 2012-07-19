<?php

namespace Armd\NewsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Armd\ListBundle\Controller\ListController;

class NewsController extends ListController
{
    /**
     * @Route("/", name="armd_news_list_index")     
     * @Route("/page/{page}", defaults={"page" = 1}, name="armd_news_list_index_by_page")     
     */
    function pressCentreAction($page = 1)
    {
        $date = new \DateTime();
        
        return $this->render($this->getTemplateName('press-centre'), array(
            'date'      => $date,
            'memorials' => $this->getMemorialEventsList($date),
            'billboard' => $this->getImportantNewsList(),
            'news'      => $this->getLatestNewsList($date, 10, $page),
        ));
    }
    
    
    
    /**
     * @Route("/archive", defaults={"year" = null, "month" = null, "day" = null}, name="armd_news_archive_index")     
     * @Route("/archive/{year}/{month}/{day}", requirements={"year" = "\d{4}", "month" = "\d{2}", "day" = "\d{2}"}, name="armd_news_archive_by_date", options={"expose"=true})
     */
    function archiveAction($year, $month, $day)
    {
        $date = $this->getDate($year, $month, $day);
        $begin = clone($date);
        $begin->sub(new \DateInterval('P14D'));

        return $this->render($this->getTemplateName('archive'), array(
            'news'  => $this->getArchiveRepository($date)->getQuery()->getResult(),
            'date'  => $date,
        ));
    }
    
    
    function thisDayListAction($month, $day)
    {
        return $this->render($this->getTemplateName('today-list'), array('entities' => $this->getThisDayListRepository($month, $day)));
    }
    
    function getMemorialEventsList($date)
    {
        return $this->getMemorialsListRepository($date)->getQuery()->getResult();
    }
    
    function getLatestNewsList($date, $limit = 10, $page = 1)
    {
        return $this->getPagination($this->getNewsListRepository($date)->getQuery(), $page, $limit, 'armd_news_list_index_by_page');
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
        
        $query = $repository->getQuery();
        
        if ($limit)
        {
            $query->setMaxResults($limit);
        }
        
        return $query->getResult();
    }
    
    function getNewsListRepository($date)
    {
        return $this->getListRepository()
            ->setFiltrableCategories()
            ->setImportant(false)
            ->setEndDate($date);
        ;
            
    }
    
    function getMemorialsListRepository($date)
    {
        return $this->getListRepository()
            ->setCategories(array('memorials'))
            ->setMonthAndDay($date)
        ;
    }
    
    /**
     * @param string \DateTime
     * @param string \DateTime
     * @return \Armd\NewsBundle\Repository\NewsRepository
     */    
    function getArchiveRepository($date, $categories = array())
    {
        $from = clone($date);
        $to = clone($date);
        
        $repository = $this->getListRepository()
            ->setBeginDate($from->setTime(0, 0, 0))
            ->setEndDate($to->setTime(23, 59, 59))
            ->setFiltrableCategories()
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

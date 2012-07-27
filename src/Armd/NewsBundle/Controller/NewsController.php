<?php

namespace Armd\NewsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Armd\ListBundle\Controller\ListController;

class NewsController extends ListController
{
    /**
     * @Route("/", name="armd_news_list_index")     
     * @Route("/page/{page}/", defaults={"page" = 1}, name="armd_news_list_index_by_page")     
     */
    function pressCentreAction($page = 1, $limit = 10)
    {
        $date = new \DateTime();
        
        return $this->render($this->getTemplateName('press-centre'), array(
            'date'      => $date,
            'news'      => $this->getLatestNewsList($date, $limit, $page),            
            'memorials' => $this->getMemorialEventsList($date),
            'billboard' => $this->getImportantNewsList(),
        ));
    }
    
    /**
     * @Route("/archive/", defaults={"year" = null, "month" = null, "day" = null}, name="armd_news_archive_index")     
     * @Route("/archive/{year}/{month}/{day}/", requirements={"year" = "\d{4}", "month" = "\d{2}", "day" = "\d{2}"}, name="armd_news_archive_by_date", options={"expose"=true})
     */
    function archiveAction($year, $month, $day)
    {
        $date = $this->getDate($year, $month, $day);
        $categories = (array) $this->getRequest()->get('category');

        return $this->render($this->getTemplateName('archive'), array(
            'date'          => $date,
            'news'          => $this->getArchiveRepository($date, $categories)->getQuery()->getResult(),
            'categories'    => $this->getCategoriesList($categories),
            'category'      => $categories,
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
        
        $categories ? $repository->setCategories($categories) : $repository->setFiltrableCategories();

        $query = $repository->getQuery();
        
        if ($limit)
        {
            $query->setMaxResults($limit);
        }
        
        return $query->getResult();
    }
    
    function getCategoriesList(array $categories)
    {
        $result = $this->getDoctrine()->getRepository('ArmdNewsBundle:Category')->findBy(array('filtrable' => '1'), array('priority' => 'ASC'));
        
        foreach ($result as $category) {
            $category->setSelected($categories ? in_array($category->getSlug(), $categories) : true);
        }
        
        return $result;
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
     * @param \DateTime $date
     * @param array     $categories
     * @return \Armd\NewsBundle\Repository\NewsRepository
     */    
    function getArchiveRepository($date, $categories = array())
    {
        $from = clone($date);
        $to = clone($date);
        
        $repository = $this->getListRepository()
            ->setBeginDate($from->setTime(0, 0, 0))
            ->setEndDate($to->setTime(23, 59, 59))
        ;
        
        $categories ? $repository->setCategories($categories) : $repository->setFiltrableCategories();
        
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

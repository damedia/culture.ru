<?php

namespace Armd\NewsBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\ListBundle\Controller\ListController;
use Armd\MkCommentBundle\Entity\Thread;

class NewsController extends Controller
{
//    function __construct(ContainerInterface $container = null)
//    {
//        $this->setContainer($container);
//    }
//
    /**
     * @Route("/rss/", defaults={"_format"="xml"}, name="armd_news_rss")
     */        
    function rssAction()
    {
        $criteria = array(
            'category' => array('news', 'interviews', 'reportages')
        );

        return $this->render('ArmdNewsBundle:News:rss.xml.twig', array(
            'news' => $this->getPagination($criteria, 1, 30),
        ));
    }        

    /**
     * @Route("/map/", name="armd_news_map")
     * @Template()
     */
    public function mapAction()
    {
        $em = $this->getDoctrine()->getManager();
        $regionsRepo = $em->getRepository('ArmdAtlasBundle:Region');
        $regions = $regionsRepo->findBy(array(), array('title'=>'ASC'));
        if (! $regions)
            throw new NotFoundHttpException("Regions not found");

        $categories = $this->getNewsManager()->getCategories();
        if (! $categories)
            throw new NotFoundHttpException("Categories not found");

        $themes = $this->getNewsManager()->getThemes();
        if (! $themes)
            throw new NotFoundHttpException("Themes not found");

        $lastNews = $this->getNewsManager()->getLastNews();
        $dateFrom = new \DateTime('-1 month');
        $dateTo   = new \DateTime('+13 month');
        $dateFromStr = $dateFrom->format('d.m.Y');
        $dateToStr   = $dateTo->format('d.m.Y');
        return array(
            'regions' => $regions,
            'categories' => $categories,
            'themes' => $themes,
            'lastNews' => $lastNews,
            'dateFrom' => $dateFromStr,
            'dateTo' => $dateToStr,
        );
    }

    /**
     * @Route("/map/filter", name="armd_news_filter", defaults={"_format"="json"})
     */
    public function filterAction()
    {
        try {
            $filter = $this->getRequest()->get('f');
            $filter['is_on_map'] = true;
            $data = $this->getNewsManager()->filterBy($filter);
            return array(
                'success' => true,
                'message' => 'OK',
                'result' => array(
                    'filter' => $filter,
                    'data' => $data,
                ),
            );
        }
        catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    /**
     * @Route("/map/balloon", name="armd_news_map_balloon")
     * @Template()
     */
    public function balloonAction()
    {
        try {
            $id = (int) $this->getRequest()->get('id');
            $entity = $this->getDoctrine()->getManager()->getRepository('ArmdNewsBundle:News')->find($id);
            return array('entity' => $entity);
        }
        catch (\Exception $e) {
            print $e->getMessage();
        }
    }

    /**
     * @Route("/", name="armd_news_list_index", options={"expose"=true})
     * @Route("/{category}/", requirements={"category" = "[a-z]+"}, name="armd_news_list_index_by_category", options={"expose"=true})
     * @Template("ArmdNewsBundle:News:index.html.twig")
     */
    function newsIndexAction($category = null)
    {
        if ($category) {
            $categoryEntity = $this->getDoctrine()->getRepository('ArmdNewsBundle:Category')
                ->findOneBySlug($category);
        } else {
            $categoryEntity = null;
        }

        return array(
            'category' => $category,
            'categoryEntity' => $categoryEntity
        );
    }


    /**
     * @Route("/two-column-news-list/{category}", name="armd_news_two_column_list", defaults={"category"=null}, options={"expose"=true})
     * @Template("ArmdNewsBundle:News:two-column-list.html.twig")
     */
    function twoColumnNewsListAction($category = null, $limit = null)
    {
        $category = $category ? array($category) : array('news', 'interviews', 'reportages');
        $request = $this->getRequest();

        if ($request->query->has('to_date') || $request->query->has('from_date')) {
            $limit = $limit ? $limit : 100;
            $criteria = array(
                'category' => $category,
            );
            if ($request->query->has('to_date')) {
                $criteria['to_date'] = new \DateTime($this->getRequest()->get('to_date'));
            }
            if ($request->query->has('from_date')) {
                $criteria['from_date'] = new \DateTime($this->getRequest()->get('from_date'));
            }
            $newsByDate = $this->getNewsManager()->getNewsGroupedByDate($criteria, $limit);
        } else {
            $limit = $limit ? $limit : 25;
            $firstLoadedDate = new \DateTime($request->get('first_loaded_date'));
            $firstLoadedDate->sub(new \DateInterval('P1D'))->setTime(0, 0);
            $newsByDate = $this->getNewsManager()->getNewsBeforeDate($category, $firstLoadedDate, $limit);
        }

        return array(
            'newsByDate' => $newsByDate
        );
    }

//    /**
//     * @Route("/list/{category}")
//     */
//    function newsListAction($category = null)
//    {
//        $criteria = array(
//            'category'  => $category,
//        );
//        if (! $category) {
//            $criteria = array(
//                'category'  => array('news', 'interviews', 'reportages'),
//            );
//        }
//
//        $request->get
//
//        return $this->render('ArmdNewsBundle:News:list.html.twig', array(
//            'category'      => $category,
//            'news'          => $this->getNewsManager()-$criteria, $page, $limit),
//        ));
//
//    }
    
    /**
     * @Route("/{category}/{id}/", requirements={"category" = "[a-z]+", "id" = "\d+"}, name="armd_news_item_by_category", options={"expose"=true})
     * @Route("/{category}/{id}/print", requirements={"category" = "[a-z]+", "id" = "\d+"}, defaults={"isPrint"=true}, name="armd_news_item_by_category_print")
     */
    function newsItemAction($id, $category, $template = null, $isPrint = false)
    {
        // menu fix
        $menu = $this->get('armd_main.menu.main');
        $menuFinder = $this->get('armd_main.menu_finder');
        if(!$menuFinder->findByUri($menu, $this->getRequest()->getRequestUri())) {
            $menu->setCurrentUri(
                $this->get('router')->generate('armd_news_list_index')
            );
        }

        $entity = $this->getDoctrine()->getManager()->getRepository('ArmdNewsBundle:News')->find($id);

        if (null === $entity) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $id));
        }

        $template = $template ? $template : 'ArmdNewsBundle:News:item.html.twig';
        $template = $isPrint ? 'ArmdNewsBundle:News:item-print.html.twig' : $template;

        $categories = $this->getNewsManager()->getCategories();

        $calendarDate = $entity->getDate();

        return $this->render($template, array(
            'entity'      => $entity,
            'category'    => $category,
            'categories'  => $categories,
            'calendarDate'  => $calendarDate,
            'comments'    => $this->getComments($entity->getThread()),
            'thread'      => $entity->getThread(),
        ));
    }
        
    function categoriesAction($category)
    {
        return $this->render('ArmdNewsBundle:News:categories.html.twig', array(
            'category'      => $category,
            'categories'    => $this->getNewsManager()->getCategories(),
        ));
    }
    
    function latestNewsAction($limit)
    {
        return $this->render('ArmdNewsBundle:News:latest-news.html.twig', array(
            'news'  => $this->getPagination(array(), 1, $limit),
        ));
    }
    
    function billboardAction($limit = 10)
    {
        $criteria = array(
            'important' => true,
        );

        $entities = $this->getNewsManager()->getBillboardNews();

        return $this->render('ArmdNewsBundle:News:billboard.html.twig', array(
            'entities' => $entities,
        ));
    }
    
    public function recommendedNewsAction($limit = 10)
    {
        $criteria = array('important' => true);
        $entities = $this->getNewsManager()->getBillboardNews();
        return $this->render('ArmdNewsBundle:News:latest-news.html.twig', array(
            'news' => $entities,
        ));
    }

    public function readAlsoNewsAction($entity, $limit = 10)
    {
        $entities = $this->getNewsManager()->getSiblingNews($entity, $limit);
        return $this->render('ArmdNewsBundle:News:read-also-news.html.twig', array(
            'entities' => $entities,
        ));
    }

    function memorialEventsAction()
    {
        $criteria = array(
            'category'      => 'memorials',
            'memorial_date' => new \DateTime(),
        );    
    
        return $this->render('ArmdNewsBundle:News:memorials.html.twig', array(
            'entities'      => $this->getPagination($criteria, 1, 10),
        ));
    }
                
    protected function getPagination($criteria, $page, $limit)
    {
        $query = $this->getNewsManager()->getQueryBuilder($criteria)->getQuery();
        return $this->get('knp_paginator')->paginate($query, $page, $limit);
    }
        
    protected function getNewsManager()
    {
        return $this->get('armd_news.manager.news');
    }        
    
    /**
     * @param \Armd\MkCommentBundle\Entity\Thread $thread
     * @return \Armd\MkCommentBundle\Entity\Comment
     */
    protected function getComments(Thread $thread = null)
    {
        if (empty($thread)) {
            return null;
        } else {
            return $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);
        }
    }

    protected function getControllerName()
    {
        return 'ArmdNewsBundle:News';
    }
}

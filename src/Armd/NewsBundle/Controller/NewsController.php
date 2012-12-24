<?php

namespace Armd\NewsBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\ListBundle\Controller\ListController;
use Armd\MkCommentBundle\Entity\Thread;

class NewsController extends ListController
{
    function __construct(ContainerInterface $container = null)
    {
        $this->setContainer($container);    
    }
    
    /**
     * @Route("/rss/", defaults={"_format"="xml"}, name="armd_news_rss")
     */        
    function rssAction()
    {
        $now = new \DateTime();
        $criteria = array(
            'category' => array('news', 'interviews', 'reportages')
        );

        return $this->render('ArmdNewsBundle:News:rss.xml.twig', array(
            'news' => $this->getPaginator($criteria, 1, 30),
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

        $lastNews = $this->getNewsManager()->getLastNews();
        $dateFrom = new \DateTime('-1 month');
        $dateTo   = new \DateTime('+13 month');
        $dateFromStr = $dateFrom->format('d.m.Y');
        $dateToStr   = $dateTo->format('d.m.Y');
        return array(
            'regions' => $regions,
            'categories' => $categories,
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
            $entity = $this->getEntityRepository()->find($id);
            return array('entity' => $entity);
        }
        catch (\Exception $e) {
            print $e->getMessage();
        }
    }

    /**
     * @Route("/", name="armd_news_list_index", options={"expose"=true})
     * @Route("/page/{page}/", requirements={"page" = "\d+"}, defaults={"page" = 1}, name="armd_news_list_index_by_page", options={"expose"=true})
     * @Route("/{category}/", requirements={"category" = "[a-z]+"}, name="armd_news_list_index_by_category", options={"expose"=true})
     * @Route("/{category}/page/{page}/", requirements={"category" = "[a-z]+", "page" = "\d+"}, defaults={"page" = 1}, name="armd_news_list_index_by_category_and_page", options={"expose"=true})
     */
    function newsListAction($category = null, $page = 1, $limit = 10)
    {
        $criteria = array(
            'category'  => $category,
        );
        if (! $category) {
            $criteria = array(
                'category'  => array('news', 'interviews', 'reportages'),
            );
        }

//        $calendarDate = $this->getRequest()->get('date');
//        if ($calendarDate) {
//            $calendarDate = new \DateTime($calendarDate);
//            $criteria['target_date'] = $calendarDate;
//        }

        return $this->render($this->getTemplateName('list'), array(
            'category'      => $category,
//            'calendarDate'  => $calendarDate,
            'news'          => $this->getPaginator($criteria, $page, $limit),
        ));
    }
    
    /**
     * @Route("/{category}/{id}/", requirements={"category" = "[a-z]+", "id" = "\d+"}, name="armd_news_item_by_category", options={"expose"=true})
     * @Route("/{category}/{id}/print", requirements={"category" = "[a-z]+", "id" = "\d+"}, defaults={"isPrint"=true}, name="armd_news_item_by_category_print")
     */
    function newsItemAction($id, $category, $template = null, $isPrint = false)
    {
        $entity = $this->getEntityRepository()->find($id);

        if (null === $entity) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $id));
        }

        $template = $template ? $template : $this->getTemplateName('item');
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
        return $this->render($this->getTemplateName('categories'), array(
            'category'      => $category,
            'categories'    => $this->getNewsManager()->getCategories(),
        ));
    }
    
    function latestNewsAction($limit)
    {
        return $this->render($this->getTemplateName('latest-news'), array(
            'news'  => $this->getPaginator(array(), 1, $limit),
        ));
    }
    
    function billboardAction($limit = 10)
    {
        $criteria = array(
            'important' => true,
        );

        $entities = $this->getNewsManager()->getBillboardNews();
    
        return $this->render($this->getTemplateName('billboard'), array(
            'entities' => $entities,
        ));
    }
    
    public function recommendedNewsAction($limit = 10)
    {
        $criteria = array('important' => true);
        $entities = $this->getNewsManager()->getBillboardNews();
        return $this->render($this->getTemplateName('latest-news'), array(
            'news' => $entities,
        ));
    }

    public function readAlsoNewsAction($entity, $limit = 10)
    {
        $entities = $this->getNewsManager()->getSiblingNews($entity, $limit);
        return $this->render($this->getTemplateName('read-also-news'), array(
            'entities' => $entities,
        ));
    }

    function memorialEventsAction()
    {
        $criteria = array(
            'category'      => 'memorials',
            'memorial_date' => new \DateTime(),
        );    
    
        return $this->render($this->getTemplateName('memorials'), array(
            'entities'      => $this->getPaginator($criteria, 1, 10),
        ));
    }
                
    function getPaginator($criteria, $page, $limit)
    {
        return $this->getPagination($this->getNewsManager()->getQueryBuilder($criteria)->getQuery(), $page, $limit);
    }
        
    function getNewsManager()
    {
        return $this->get('armd_news.manager.news');
    }        
    
    /**
     * @param \Armd\MkCommentBundle\Entity\Thread $thread
     * @return \Armd\MkCommentBundle\Entity\Comment
     */
    public function getComments(Thread $thread = null)
    {
        if (empty($thread)) {
            return null;
        } else {
            return $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);
        }
    }

    function getControllerName()
    {
        return 'ArmdNewsBundle:News';
    }            
}

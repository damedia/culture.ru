<?php

namespace Armd\NewsBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Armd\ListBundle\Controller\ListController;
use Armd\CommentBundle\Entity\Thread;

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
        return $this->render('ArmdNewsBundle:News:rss.xml.twig', array(
            'news' => $this->getLatestNewsList(),
        ));
    }        
    
    /**
     * @Route("/", name="armd_news_list_index")     
     * @Route("/page/{page}/", requirements={"page" = "\d+"}, name="armd_news_list_index_by_page")
     * @Route("/{category}/", requirements={"category" = "[a-z]+"}, name="armd_news_list_index_by_category")
     * @Route("/{category}/page/{page}/", requirements={"category" = "[a-z]+", "page" = "\d+"}, name="armd_news_list_index_by_category_and_page")                    
     */
    function newsListAction($category = null, $page = 1, $limit = 10)
    {
        $criteria = array(
            'category'  => $category,
        );    
    
        return $this->render($this->getTemplateName('list'), array(
            'category'      => $category,
            'news'          => $this->getPaginator($criteria, $page, $limit),
        ));
    }
    
    /**
     * @Route("/{category}/{id}/", requirements={"category" = "[a-z]+", "id" = "\d+"}, name="armd_news_item_by_category")     
     */    
    function newsItemAction($id, $category)
    {
        $entity = $this->getEntityRepository()->find($id);

        if (null === $entity) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $id));
        }

        return $this->render($this->getTemplateName('item'), array(
            'entity'        => $entity,
            'category'      => $category,
            'comments'    => $this->getComments($entity->getThread()),
            'thread'      => $entity->getThread(),
        ));
    }
        
    function categoriesAction($category)
    {
        return $this->render($this->getTemplateName('categories'), array(
            'categories'    => $this->getCategoriesList(array($category)),
        ));
    }
    
    function latestNewsAction($limit)
    {
        return $this->render($this->getTemplateName('latest-news'), array(
            'news'          => $this->getPaginator(array(), 1, $limit),
        ));
    }
    
    function billboardAction($limit = 10)
    {
        $criteria = array(
            'important'  => true,
        );        
    
        return $this->render($this->getTemplateName('billboard'), array(
            'entities'          => $this->getPaginator($criteria, 1, $limit),
        ));
    }
    
    function memorialEventsAction()
    {
        $criteria = array(
            'category'      => 'memorial',
            'memorial_date' => new \DateTime(),
        );    
    
        return $this->render($this->getTemplateName('memorials'), array(
            'entities'      => $this->getPaginator($criteria, 1, 10),
        ));
    }
                
    function getCategoriesList(array $categories = array())
    {
        $result = $this->getDoctrine()->getRepository('ArmdNewsBundle:Category')->findBy(array('filtrable' => '1'), array('priority' => 'ASC'));
        
        foreach ($result as $category) {
            $category->setSelected($categories ? in_array($category->getSlug(), $categories) : true);
        }
        
        return $result;
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
     * @param Armd\CommentBundle\Entity\Thread $thread
     * @return \Armd\CommentBundle\Entity\Comment
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

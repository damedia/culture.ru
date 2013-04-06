<?php

namespace Armd\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Armd\NewsBundle\Entity\NewsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\AtlasBundle\Entity\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SpecialController extends Controller
{
    function indexAction()
    {    
        $categories = $this->getNewsManager()->getCategories();
        
        $newRussiaImages = $this->get('armd_atlas.manager.object')->findObjects(
            array(
                ObjectManager::CRITERIA_RUSSIA_IMAGES => true,
                ObjectManager::CRITERIA_ORDER_BY => array('createdAt' => 'DESC'),
                ObjectManager::CRITERIA_LIMIT => 4
            )
        );
        
		return $this->render('ArmdMainBundle:Special:index.html.twig', array(
            'categories'    => $categories,		
			'news'          => $this->getNews($categories),
            'newRussiaImages' => $newRussiaImages         
        ));
    }
    
    public function russianImagesAction()
    {
        return $this->render('ArmdMainBundle:Special:russian-images.html.twig', array(
            'objects'   => $this->getObjectManager()->getRussiaImagesList($this->getRequest()->get('searchString')),
        ));
    }    
    
    public function russianImagesItemAction($id, $print=0)
    {
    	$entity = $this->getObjectManager()->getObject($id);
    	
        $relatedObjects = $this->getObjectManager()->findObjects
        (
            array(
                ObjectManager::CRITERIA_LIMIT => 5,
                ObjectManager::CRITERIA_RUSSIA_IMAGES => true,
                ObjectManager::CRITERIA_TAGS => $entity->getTags(),
                ObjectManager::CRITERIA_RANDOM => true,
                ObjectManager::CRITERIA_NOT_IDS => array($entity->getId()),
            )
        );    	
    	
        if ($print)
	        return $this->render('ArmdMainBundle:Special:russian-images-item-print.html.twig', array(
	            'entity'    => $entity,
	            'relatedObjects' => $relatedObjects
	        )); 
        else
	        return $this->render('ArmdMainBundle:Special:russian-images-item.html.twig', array(
	            'entity'    => $entity,
	            'relatedObjects' => $relatedObjects
	        ));        
    }    
	
	public function aboutAction()
    {
        return $this->render('ArmdMainBundle:Special:about.html.twig', array(
        ));
    }
    	
	public function chroniclesAction()
    {
        $centuries = $this->get('armd_chronicle.controller.event')->getCenturiesList();
        
		return $this->render('ArmdMainBundle:Special:chronicles.html.twig', array(
            'centuries' => $centuries,
        ));
    }
	
	public function servicesAction()
    {
        return $this->render('ArmdMainBundle:Special:services.html.twig', array(
        ));
    }
    
	function getNews(array $categories)
    {
        $result = array();

        foreach ($categories as $c)
        {
            $result[$c->getSlug()] =  $this->getNewsManager()->findObjects(array(
                NewsManager::CRITERIA_LIMIT => 4,
                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array($c->getSlug())
            ));
        }
        
        return $result;
    }
	
    function newsAction($category, $tag = 'important', $limit = 10, $offset = 0)
    {
        $criteria = array(
            NewsManager::CRITERIA_LIMIT => $limit,
            NewsManager::CRITERIA_OFFSET => $offset, 
            NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array($category),
            NewsManager::CRITERIA_IMPORTANT => $tag === 'important' ? true : null
        );

        return $this->render('ArmdMainBundle:Special:news.html.twig', array(
            'news'  =>  $this->getNewsManager()->findObjects($criteria),
            'category' => $category,
        ));        
    }
    
    function newsIndexAction()
    {   

    	$request = $this->getRequest(); 
	    $categories = $this->getNewsManager()->getCategories();
        $searchQuery = $request->get('search_query');
        
		return $this->render('ArmdMainBundle:Special:news.html.twig', array(
            'categories'    => $categories,		
			'news'          => $this->getNews($categories), 
			'searchQuery' => $searchQuery,
			'show_more' => true  
        ));
    }
            
    function getEventManager()
    {
        return $this->get('armd_event.manager.event');
    }

    function getNewsManager()
    {
        return $this->get('armd_news.manager.news');
    }
    
    function getObjectManager()
    {
        return $this->get('armd_atlas.manager.object');
    }    
    
    /**
	 * @Template("ArmdMainBundle:Special:text_search_result.html.twig")
     *
     */
    public function newstextSearchResultAction()
    {
        $request = $this->getRequest();
        $limit = $request->get('limit', 20);
        if ($limit > 100) {
            $limit = 100;
        }

        $news = $this->getNewsManager()->findObjects(
            array(
                NewsManager::CRITERIA_SEARCH_STRING => $request->get('search_query'),
                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array($request->get('category_slug')),
                NewsManager::CRITERIA_LIMIT => $limit,
                NewsManager::CRITERIA_OFFSET => $request->get('offset')
            )
        );

        return array(
            'news' => $news
        );
    }
    
    
    /**
	 * @Template("ArmdMainBundle:Special:text_search_result.html.twig")
     *
     */
    function newsMoreLoadAction($category = 'news', $limit = 10, $offset = 0)
    {
    	$request = $this -> getRequest();
    	
    	if ($request -> get('category'))
    		$category = $request -> get('category');
    		
    	if ($request -> get('limit'))
    		$limit = $request -> get('limit');    		
    		
    	if ($request -> get('offset'))
    		$offset = $request -> get('offset');    		
    	
    	$news = $this->getNewsManager()->findObjects(
        	array(
	            NewsManager::CRITERIA_LIMIT => $limit,
    	        NewsManager::CRITERIA_OFFSET => $offset, 
        	    NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array($category),
            	NewsManager::CRITERIA_ORDER_BY => array('newsDate' => 'DESC')
        	)
        );

        return array(
            'news' => $news
        );       
    }       
}

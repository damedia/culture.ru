<?php

namespace Armd\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Armd\NewsBundle\Entity\NewsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\AtlasBundle\Entity\ObjectManager;

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
    
    public function russianImagesItemAction($id)
    {
        return $this->render('ArmdMainBundle:Special:russian-images-item.html.twig', array(
            'entity'    => $this->getObjectManager()->getObject($id),
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
	
    function newsAction($category, $tag = 'important', $limit = 10)
    {
        $criteria = array(
            NewsManager::CRITERIA_LIMIT => $limit,
            NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array($category),
            NewsManager::CRITERIA_IMPORTANT => $tag === 'important' ? true : null
        );

        return $this->render('ArmdMainBundle:Special:news.html.twig', array(
            'news'  =>  $this->getNewsManager()->findObjects($criteria)
        ));        
    }
    
    function newsIndexAction()
    {    
        $categories = $this->getNewsManager()->getCategories();
        
		return $this->render('ArmdMainBundle:Special:news.html.twig', array(
            'categories'    => $categories,		
			'news'          => $this->getNews($categories),        
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
}

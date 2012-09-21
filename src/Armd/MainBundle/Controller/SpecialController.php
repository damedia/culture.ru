<?php

namespace Armd\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SpecialController extends Controller
{
    function indexAction()
    {    
        $categories = $this->getNewsManager()->getCategories();
        
		return $this->render('ArmdMainBundle:Special:index.html.twig', array(
            'categories'    => $categories,		
			'news'          => $this->getNews($categories),        
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
            $result[$c->getSlug()] = $this->get('armd_news.controller.news')->getPaginator(array('category' => $c->getSlug()), 1, 4);    
        }
        
        return $result;
    }
	
    function newsAction($category, $tag = 'important', $limit = 10)
    {
        $criteria = array(
            $tag => true, 
            'category' => $category
        );
    
        return $this->render('ArmdMainBundle:Special:news.html.twig', array(
            'news'  =>  $this->getNewsManager()->getPager($criteria, 1, $limit),
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
}

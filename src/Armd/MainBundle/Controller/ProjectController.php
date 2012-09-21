<?php

namespace Armd\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    function indexAction()
    {    
        return $this->render('ArmdMainBundle:Project:index.html.twig', array(
        ));
    }
    
    function eventsAction()
    {        
        $request = Request::createFromGlobals();
        
        $criteria = array(
            'region_id' => $request->query->get('region_id'),
            'month'     => $request->query->get('month'),
        );
        
        $manager = $this->getEventManager();

        return $this->render('ArmdMainBundle:Project:events.html.twig', array(
            'events'    => $manager->getPager($criteria, 1),
            'regions'   => $manager->getDistinctRegions(),
            'months'    => $manager->getDistinctMonths(),
            'filter'    => $criteria,
        ));
    }
    
    function newsAction($category, $tag = 'Project', $limit = 10)
    {
        $criteria = array(
            $tag => true, 
            'category' => $category
        );
    
        return $this->render('ArmdMainBundle:Project:news.html.twig', array(
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

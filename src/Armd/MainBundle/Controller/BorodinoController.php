<?php

namespace Armd\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BorodinoController extends Controller
{
    function indexAction()
    {    
        return $this->render('ArmdMainBundle:Borodino:index.html.twig', array(
        ));
    }
    
    function eventsAction($category = 'borodino')
    {        
        $request = Request::createFromGlobals();
        
        $criteria = array(
            'category'  => $category,
            'region_id' => $request->query->get('region_id'),
            'month'     => $request->query->get('month'),
        );
        
        $manager = $this->getEventManager();

        return $this->render('ArmdMainBundle:Borodino:events.html.twig', array(
            'events'    => $manager->getPager($criteria, 1),
            'regions'   => $manager->getDistinctRegions(),
            'months'    => $manager->getDistinctMonths(),
            'filter'    => $criteria,
        ));
    }
    
    function newsAction($category, $subject = 'borodino', $limit = 10)
    {
        $criteria = array(
            'subject'   => $subject, 
            'category'  => $category,
        );
    
        return $this->render('ArmdMainBundle:Borodino:news.html.twig', array(
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

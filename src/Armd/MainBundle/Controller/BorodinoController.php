<?php

namespace Armd\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BorodinoController extends Controller
{
    function indexAction()
    {    
        return $this->render('ArmdMainBundle:Borodino:index.html.twig', array(
        ));
    }
    
    function eventsAction()
    {
        return $this->render('ArmdMainBundle:Borodino:events.html.twig', array(
            'events'    =>  $this->getEventManager()->getList(),
        ));
    }
    
    function newsAction($category, $tag = 'borodino', $limit = 10)
    {
        return $this->render('ArmdMainBundle:Borodino:news.html.twig', array(
            'news'  =>  $this->getNewsManager()->getPager(array($tag => true, 'category' => $category), 1, $limit),
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

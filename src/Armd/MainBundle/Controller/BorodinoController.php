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
        
    function borodinoNewsAction($category)
    {
        $news = $this->get('armd_news.controller.news')->getLatestNewsList(4, 1, array($c->getSlug()));    
        
        return $this->render("ArmdMainBundle:Borodino:news.html.twig");
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

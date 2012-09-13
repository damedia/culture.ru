<?php

namespace Armd\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BorodinoController extends Controller
{
    function indexAction()
    {
        return $this->render("ArmdMainBundle:Borodino:index.html.twig");
    }
    
    function borodinoNewsAction($category)
    {
        $news = $this->get('armd_news.controller.news')->getLatestNewsList(4, 1, array($c->getSlug()));    
        
        return $this->render("ArmdMainBundle:Borodino:news.html.twig");
    }
}

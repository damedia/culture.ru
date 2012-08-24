<?php

namespace Armd\MainBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\ListBundle\Controller\ListController;
use Armd\ListBundle\Repository\BaseRepository;

class MainController extends Controller
{
    public function indexAction()
    {
        $categories = $this->get('armd_news.controller.news')->getCategoriesList();



        return $this->render('ArmdMainBundle::index.html.twig', array(
            'news'          => $this->getNews($categories),        
            'categories'    => $categories,
            'russiaImages'  => $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object')->findRussiaImages(),
        ));
    }

    public function randomRussiaImagesAction()
    {
        return $this->render('ArmdMainBundle:Main:randomRussiaImages.html.twig', array(
            'russiaImages' => $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object')->findRandomRussiaImages(6)
        ));
    }
    
    function getNews(array $categories)
    {
        $result = array();
        
        foreach ($categories as $c)
        {
            $result[$c->getSlug()] = $this->get('armd_news.controller.news')->getLatestNewsList(4, 1, array($c->getSlug()));    
        }
        
        return $result;
    }
    
    function getTemplateName($action)
    {
        return "{$this->getControllerName()}:{$action}.html.twig";
        
    }    
    
    function getControllerName()
    {
        return 'ArmdMainBundle:Main';
    }        
}

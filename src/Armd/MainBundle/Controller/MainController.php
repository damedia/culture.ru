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
        ));   
    }
    
    function getNews(array $categories)
    {
        $result = array();
        
        foreach ($categories as $c)
        {
            $result[$c->getSlug()] = $this->get('armd_news.controller.news')->getImportantNewsList(array($c->getSlug()), 4);    
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

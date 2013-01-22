<?php

namespace Armd\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    function indexAction($template = 'ArmdMainBundle:Project:index.html.twig')
    {
        // fix current menu item
        $routeName = $this->getRequest()->get('_route');
        if ($routeName === 'armd_main_project_1150') {
            $this->get('armd_main.menu.main')->setCurrentUri(
                $this->get('router')->generate('armd_main_project_1150')
            );
        }


        return $this->render($template);
    }
    
    function eventsAction($subject)
    {        
        $request = Request::createFromGlobals();
        
        $criteria = array(
            'subject'   => $subject,
            'region_id' => $request->query->get('region_id'),
            'month'     => $request->query->get('month'),
        );
        
        $manager = $this->getEventManager();

        return $this->render('ArmdMainBundle:Project:events.html.twig', array(
            'events'    => $manager->getPager($criteria, 1),
            'regions'   => $manager->getDistinctRegions($subject),
            'months'    => $manager->getDistinctMonths($subject),
            'filter'    => $criteria,
        ));
    }
    
    function newsAction($category, $subject, $limit = 10)
    {
        $criteria = array(
            'subject'   => $subject, 
            'category'  => $category,
        );
    
        return $this->render('ArmdMainBundle:Project:news.html.twig', array(
            'news'      =>  $this->getNewsManager()->getPager($criteria, 1, $limit),
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

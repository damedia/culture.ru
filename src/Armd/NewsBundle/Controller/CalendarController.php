<?php

namespace Armd\NewsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Armd\ListBundle\Controller\BaseController;

use Armd\NewsBundle\Calendar\Calendar;

class CalendarController extends BaseController
{
    function widgetAction($year, $month, $day)
    {
        $calendar = new Calendar(\DateTime::createFromFormat('Ymd', "{$year}{$month}{$day}"));

        return $this->render($this->getTemplateName('widget'), array('calendar' => $calendar->get()));
    }

    /**
     * @Route("/request/{year}/{month}", requirements={"year" = "\d{4}", "month" = "\d{2}"})     
     */        
    function requestAction($year, $month)
    {
        $calendar = new Calendar(\DateTime::createFromFormat('Ymd', "{$year}{$month}01"));
        
        $response = new Response(json_encode($calendar));
        $response->headers->set('Content-Type', 'application/json');        
    }    
    
    
    function getControllerName()
    {
        return 'ArmdNewsBundle:Calendar';
    }    
}
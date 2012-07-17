<?php

namespace Armd\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Armd\NewsBundle\Calendar\Calendar;

class CalendarController extends Controller
{
    /**
     * @Route("/calendar", defaults={"year" = null, "month" = null, "day" = null})         
     * @Route("/calendar/{year}/{month}/{day}", requirements={"year" = "\d{4}", "month" = "\d{2}", "day" = "\d{2}"})     
     */    
    function widgetAction($year, $month, $day)
    {
        $calendar = Calendar::get(\DateTime::createFromFormat('Y.m.d', "{$year}.{$month}.{$day}"));
        var_dump($calendar);
        return $this->render($this->getTemplateName('widget'), array('calendar' => $calendar));
    }
    
    function getTemplateName($action)
    {
        return "{$this->getControllerName()}:{$action}.html.twig";
    }

    function getControllerName()
    {
        return 'ArmdNewsBundle:Calendar';
    }    
}
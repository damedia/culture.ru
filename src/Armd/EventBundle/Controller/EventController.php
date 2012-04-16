<?php

namespace Armd\EventBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Armd\Bundle\NewsBundle\Controller\NewsController as BaseController;

class EventController extends BaseController
{
    public function archiveAction($from, $to, $page)
    {
        $from = !empty($from) ? $from : date('Y-m-d');
        $to   = !empty($to)   ? $to   : date('Y-m-d', strtotime('+3days'));
        $fromDay = date('j', strtotime($from));
        $toDay   = date('j', strtotime($to));
        $entities = $this->getEntityRepository()->findByDatePeriod($from, $to, $page);
        return $this->renderCms(array(
            'entities' => $entities,
            'selectDays' => array(
                'from' => $fromDay,
                'to'   => $toDay,
            ),
        ));
    }

    public function fetchAction()
    {
        $from = $this->getRequest()->query->get('from', date('Y-m-d'));
        $to = $this->getRequest()->query->get('to', date('Y-m-d', strtotime('+4days')));
        $page = (int) $this->getRequest()->query->get('page', 1);
        $em = $this->getDoctrine()->getEntityManager();
        $entities = $em->getRepository('ArmdEventBundle:Event')->findByDatePeriod($from, $to, $page);
        if (! $entities) {
            //throw $this->createNotFoundException('Unable to find Event entities.');
            exit;
        } else {
            return $this->render('ArmdEventBundle:Event:fetch.html.twig', array(
                'entities' => $entities,
            ));
        }
    }

}

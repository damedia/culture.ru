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
        $requestQuery = $this->getRequest()->query;
        $from = $requestQuery->get('from', date('Y-m-d'));
        $to   = $requestQuery->get('to', date('Y-m-d', strtotime('+4days')));
        $page = (int) $requestQuery->get('page', 1);
        $stream = (int) $requestQuery->get('stream', 0);
        $em = $this->getDoctrine()->getEntityManager();
        $entities = $em->getRepository('ArmdEventBundle:Event')->findByDatePeriod($from, $to, $page, $stream);
        if (! $entities) {
            //throw $this->createNotFoundException('Unable to find Event entities.');
            exit;
        } else {
            if ($stream) {
                return $this->render('ArmdEventBundle:Event:fetchstream.html.twig', array(
                    'entities' => $entities,
                ));
            } else {
                return $this->render('ArmdEventBundle:Event:fetch.html.twig', array(
                    'entities' => $entities,
                ));
            }
        }
    }

}

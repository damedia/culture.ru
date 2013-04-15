<?php

namespace Armd\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PollArchiveController extends Controller {
    
    /**
     * @Route("/poll/archive/{page}", name="armd_poll_archive", requirements={"page" = "\d+"}, defaults={"page" = "1"})
     * @Template("ArmdMainBundle:Poll:archive.html.twig")
     */
    public function pollArchiveAction($page)
    {
        
        $poll_controller = $this -> get('armd_poll.controller.poll');
        return $poll_controller -> pollArchiveAction($page);
        
    }

    /**
     * @Route("/poll/archive/result/{pollId}", name="armd_poll_archive_result", requirements={"pollId" = "\d+"})
     * @Template("ArmdMainBundle:Poll:result.html.twig")
     */
    public function pollResultAction($pollId)
    {
        $poll_controller = $this -> get('armd_poll.controller.poll');
        
        $result = array();
        $poll = $this->getDoctrine()->getManager()->getRepository('ArmdPollBundle:Poll')->find($pollId);
        if (empty($poll)) {
            throw new \InvalidArgumentException('Poll');
        }
        $request = $this->getRequest();


        if ($poll_controller->canRevealPollResults($poll, $request)) {
            $result['poll'] = $poll;
            $result['message'] = null;
        }
        else {
            $result['message'] = 'Результаты опроса недоступны';
        }

        return $result;
    }       
    
}
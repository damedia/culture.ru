<?php

namespace Armd\LectureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route("/rebuild_tree")
     */
    public function rebuildTreeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repairer = new \Armd\AtlasBundle\Util\TreeRepairer();
        $repairer->rebuildTree($em, $em->getRepository('ArmdLectureBundle:LectureCategory'));
        return new Response('ok');
    }

    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }
}

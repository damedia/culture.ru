<?php

namespace Armd\LectureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;


class AdminController extends Controller
{
    /**
     * @Route("/test")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function testAction()
    {
    //        $typeCategories = $this->getDoctrine()->getRepository('ArmdLectureBundle:Lecture')->getTypeCategories();
    //        var_dump($typeCategories);
        return new Response();
    }

    /**
     * @Route("/rebuild_tree")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function rebuildTreeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repairer = new \Armd\AtlasBundle\Util\TreeRepairer();
        $repairer->rebuildTree($em, $em->getRepository('ArmdLectureBundle:LectureCategory'));
        return new Response('ok');
    }
}
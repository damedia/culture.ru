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
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lectureRepo = $em->getRepository('ArmdLectureBundle:Lecture');

        $lectureTypes = $em->getRepository('ArmdLectureBundle:LectureType')->findAll();
        $lectureCategories = $em->getRepository('ArmdLectureBundle:LectureCategory')->childrenHierarchy();

        $recommendedLectures = $lectureRepo->findRecommended();
        $lastLectures = $lectureRepo->findLastAdded();

        return array(
            'lectureTypes' => $lectureTypes,
            'lectureCategories' => $lectureCategories,
            'recommendedLectures' => $recommendedLectures,
            'lastLectures' => $lastLectures
        );
    }

    /**
     * @Route("/list")
     * @Template()
     */
    public function lectureListAction()
    {

    }

    /**
     * @Route("/category_list")
     */
    public function categoryListAction()
    {

    }

}

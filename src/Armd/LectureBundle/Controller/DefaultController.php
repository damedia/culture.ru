<?php

namespace Armd\LectureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/test")
     */
    public function testAction()
    {
        // DBG: remove this action
        $typeCategories = $this->getDoctrine()->getRepository('ArmdLectureBundle:Lecture')->getTypeCategories();
        var_dump($typeCategories);
        return new Response();
    }

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
     * @Route("/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="armd_lecture_index")
     * @Template()
     */
    public function indexAction($page)
    {
        $em = $this->getDoctrine()->getManager();
        $lectureRepo = $em->getRepository('ArmdLectureBundle:Lecture');

        // lecture types and categories
        $lectureTypes = $em->getRepository('ArmdLectureBundle:LectureType')->findAll();
        $lectureCategories = $em->getRepository('ArmdLectureBundle:LectureCategory')->childrenHierarchy();
        if(isset($lectureCategories[0]['__children'])) {
            $lectureCategories = $lectureCategories[0]['__children'];
        } else {
            $lectureCategories = array();
        }

        // recommended and last lectures
        $recommendedLectures = $lectureRepo->findRecommended();
        $lastLectures = $lectureRepo->findLastAdded();


        $typeCategories = $this->getDoctrine()->getRepository('ArmdLectureBundle:Lecture')->getTypeCategories();

        return array(
            'lectureTypes' => $lectureTypes,
            'lectureCategories' => $lectureCategories,
            'recommendedLectures' => $recommendedLectures,
            'lastLectures' => $lastLectures,
            'page' => $page,
            'typeCategories' => $typeCategories
        );
    }

    /**
     * @Route("/list/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="armd_lecture_list", options={"expose"=true})
     * @Template()
     */
    public function lectureListAction($page)
    {
        $request = $this->getRequest();
        $sortBy = $request->get('sortBy', 'date');
        $categories = $request->get('categories');
        $types = $request->get('types');
        $searchString = $request->get('searchString');

        $manager = $this->get('armd_lecture.manager.lecture');

         // DBG: remove perpage limit
        $lectures = $manager->findFiltered($page, 3, $types, $categories, $sortBy, $searchString);

        return array(
            'pagination' => $lectures,
            'sortBy' => $sortBy
        );
    }

    /**
     * @Route("/view/{id}", requirements={"id"="\d+"}, name="armd_lecture_view")
     * @Template()
     */
    public function lectureDetailsAction($id)
    {
        $lecture = $this->getDoctrine()->getManager()
            ->getRepository('ArmdLectureBundle:Lecture')->find($id);
        if(!$lecture) {
            throw $this->createNotFoundException('Lecture not found');
        }

        return array(
            'lecture' => $lecture
        );
    }


    /**
     * @Route("/category_list")
     */
    public function categoryListAction()
    {

    }

}

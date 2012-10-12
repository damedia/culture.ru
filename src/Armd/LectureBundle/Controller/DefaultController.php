<?php

namespace Armd\LectureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route("/lecture/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="armd_lecture_lecture_index")
     * @Template()
     */
    public function lectureIndexAction($page)  {
        return $this->forward('ArmdLectureBundle:Default:index', array(
            'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_LECTURE',
            'page' => $page
        ));
    }

    /**
     * @Route("/translation/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="armd_lecture_translation_index")
     * @Template()
     */
    public function translationIndexAction($page)  {
        return $this->forward('ArmdLectureBundle:Default:index', array(
            'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION',
            'page' => $page
        ));
    }

    /**
     * @Route("/cinema/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="armd_lecture_cinema_index")
     * @Template()
     */
    public function cinemaIndexAction($page)  {
        return $this->forward('ArmdLectureBundle:Default:index', array(
            'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_CINEMA',
            'page' => $page
        ));
    }

    /**
     * @Route("/index/{lectureSuperTypeCode}/{page}", requirements={"page"="\d+"}, defaults={"page" = 1})
     * @Template()
     */
    public function indexAction($lectureSuperTypeCode, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $lectureRepo = $em->getRepository('ArmdLectureBundle:Lecture');
        $categoryRepo =  $em->getRepository('ArmdLectureBundle:LectureCategory');

        $superType = $em->getRepository('ArmdLectureBundle:LectureSuperType')->findOneByCode($lectureSuperTypeCode);
        $rootCategory = $categoryRepo->findOneByLectureSuperType($superType);

        // lecture types and categories
        $lectureTypes = $em->getRepository('ArmdLectureBundle:LectureType')->findAll();
        $lectureCategories = $em->getRepository('ArmdLectureBundle:LectureCategory')->childrenHierarchy($rootCategory);

//        if(isset($lectureCategories[0]['__children'])) {
//            $lectureCategories = $lectureCategories[0]['__children'];
//        } else {
//            $lectureCategories = array();
//        }

        // recommended and last lectures
        $lastLectures = $lectureRepo->findLastAdded($superType);

        $typeCategories = $lectureRepo->getTypeCategories($superType);

        return array(
            'lectureSuperTypeCode' => $superType->getCode(),
            'lectureSuperType' => $superType,
            'lectureTypes' => $lectureTypes,
            'lectureCategories' => $lectureCategories,
            'lastLectures' => $lastLectures,
            'page' => $page,
            'typeCategories' => $typeCategories
        );
    }



    /**
     * @Route("/list/{lectureSuperTypeCode}/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="armd_lecture_list", options={"expose"=true})
     * @Template()
     */
    public function lectureListAction($lectureSuperTypeCode, $page)
    {
        $request = $this->getRequest();
        $sortBy = $request->get('sortBy', 'date');
        $categories = $request->get('categories');
        $types = $request->get('types');
        $searchString = $request->get('searchString');

        $superType = $this->getDoctrine()->getManager()
            ->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode($lectureSuperTypeCode);

        $manager = $this->get('armd_lecture.manager.lecture');

        $lectures = $manager->findFiltered($superType, $page, 20, $types, $categories, $sortBy, $searchString);

        return array(
            'pagination' => $lectures,
            'sortBy' => $sortBy
        );
    }

    /**
     * View lecture details.
     * Version can be one of these: full, trailer
     *
     * @Route("/view/{id}/{version}", requirements={"id"="\d+"}, name="armd_lecture_view", defaults={"version" = "trailer"})
     * @Template()
     */
    public function lectureDetailsAction($id, $version)
    {
        $lecture = $this->getDoctrine()->getManager()
            ->getRepository('ArmdLectureBundle:Lecture')->find($id);
        if(!$lecture) {
            throw $this->createNotFoundException('Lecture not found');
        }

        return array(
            'lecture' => $lecture,
            'lectureVersion' => $version
        );
    }


    /**
     * @Route()
     * @Template()
     */
    public function rightColumnAction()
    {
        $em = $this->getDoctrine()->getManager();
        $russiaImages = $em->getRepository('ArmdAtlasBundle:Object')->findRussiaImages(6);
        return array(
            'russiaImages' => $russiaImages
        );
    }

}

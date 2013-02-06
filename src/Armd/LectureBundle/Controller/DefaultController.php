<?php

namespace Armd\LectureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\LectureBundle\Entity\LectureManager;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/lecture/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="armd_lecture_lecture_index")
     */
    public function lectureIndexAction($page)
    {
        return $this->forward('ArmdLectureBundle:Default:index', array(
            'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_LECTURE',
            'page' => $page
        ));
    }

    /**
     * @Route("/translation/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="armd_lecture_translation_index")
     */
    public function translationIndexAction($page)
    {
        return $this->forward('ArmdLectureBundle:Default:index', array(
            'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION',
            'page' => $page
        ));
    }

    /**
     * @Route("/cinema/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="armd_lecture_cinema_index")
     */
    public function cinemaIndexAction($page)
    {
        return $this->forward('ArmdLectureBundle:Default:index', array(
            'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_CINEMA',
            'page' => $page
        ));
    }

    /**
     * @Route("/index/{lectureSuperTypeCode}/{page}", requirements={"page"="\d+"}, defaults={"page" = 1}, name="armd_lecture_default_index")
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

        // recommended and last lectures
        if ($lectureSuperTypeCode === 'LECTURE_SUPER_TYPE_CINEMA') {
            $template = 'ArmdLectureBundle:Default:index_tile.html.twig';
        } else {
            $template = 'ArmdLectureBundle:Default:index_list.html.twig';
        }
        $lastLectures = $lectureRepo->findLastAdded($superType,  2);

        $typeCategories = $lectureRepo->getTypeCategories($superType);

        return $this->render($template, array(
            'lectureSuperType' => $superType,
            'lectureTypes' => $lectureTypes,
            'lectureCategories' => $lectureCategories,
            'lastLectures' => $lastLectures,
            'page' => $page,
            'typeCategories' => $typeCategories
        ));
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

        if ($lectureSuperTypeCode === 'LECTURE_SUPER_TYPE_CINEMA') {
            $template = 'ArmdLectureBundle:Default:lecture_tile.html.twig';
            $perPage = 20;
        } else {
            $template = 'ArmdLectureBundle:Default:lecture_list.html.twig';
            $perPage = 8;
        }
        //$this->get('knp_paginator')->paginate($query, $page, $limit);
        $lectures = $manager->findFiltered($superType, $page, $perPage, $types, $categories, $sortBy, $searchString);

        return $this->render($template, array(
            'categories' => $categories,
            'pagination' => $lectures,
            'sortBy' => $sortBy
        ));
    }

    /**
     * View lecture details.
     * Version can be one of these: full, trailer
     *
     * @Route("/view/{id}/{version}", requirements={"id"="\d+"}, name="armd_lecture_view", defaults={"version" = "trailer"})
     *
     */
    public function lectureDetailsAction($id, $version)
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_lecture_default_list')
        );

        $lecture = $this->getDoctrine()->getManager()
            ->getRepository('ArmdLectureBundle:Lecture')->find($id);
        if(!$lecture) {
            throw $this->createNotFoundException('Lecture not found');
        }
        $this->getTagManager()->loadTagging($lecture);

        $manager = $this->get('armd_lecture.manager.lecture');
        $rolesPersons = $manager->getStructuredRolesPersons($lecture);

        // Форма фильтра по категориям и тематике
        $superTypeList = $this->getDoctrine()->getManager()
            ->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findAll();

        //var_dump($lecture->getMediaLectureVideo());

        return $this->render('ArmdLectureBundle:Default:lecture_details.html.twig', array(
            'superTypeList' => $superTypeList,
            'referer' => $this->getRequest()->headers->get('referer'),
            'lecture' => $lecture,
            'lectureVersion' => $version,
            'lectureRolesPersons' => $rolesPersons,
        ));
    }

    /**
     * @Route("/last-lectures/{lectureSuperTypeCode}/{limit}")
     */
    public function lastLecturesAction($lectureSuperTypeCode, $limit)
    {
        $em = $this->getDoctrine()->getManager();
        $lectureSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneBy(array('code' => $lectureSuperTypeCode));
        $lectures = $em->getRepository('ArmdLectureBundle:Lecture')
            ->findLastAdded($lectureSuperType, $limit);

        return $this->render('ArmdLectureBundle:Default:last_lectures.html.twig', array(
            'lectures' => $lectures
        ));
    }

    /**
     * @Route("/right-column")
     * @Template()
     */
    public function rightColumnAction()
    {
        $em = $this->getDoctrine()->getManager();
        $russiaImages = $em->getRepository('ArmdAtlasBundle:Object')->findRussiaImages(6);
        return $this->render('ArmdLectureBundle:Default:right_column.html.twig', array(
            'russiaImages' => $russiaImages
        ));
    }

    /**
     * Experimental video index
     *
     * @Route("/", options={"expose"=true}, name="armd_lecture_default_list")
     * @Template()
     */
    public function listAction()
    {
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_lecture_default_list')
        );

        $manager = $this->get('armd_lecture.manager.lecture');
        $request = $this->getRequest();

        // Форма фильтра
        $page      = $request->get('page', 1);
        $perPage   = $request->get('perPage', 16);
        $sort      = 'date';
        $typeIds   = null;
        $categoryIds = null;
        $searchString = '';
        $superTypeId = $request->get('supertype') ? $request->get('supertype') : null;

        $genreId = (int) $request->get('genre');
        if ($genreId) {
            $categoryIds = array($genreId);
        }

        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_lecture_default_list', array('supertype' => $superTypeId))
        );


        $lectures = $manager->findFiltered($superTypeId, $page, $perPage, $typeIds, $categoryIds, $sort, $searchString);

        if ($lectures) {
            $lecturesItems = isset($lectures['items']) ? $lectures['items'] : false;
            $lecturesTotal = $lectures['total'];
        } else {
            $lecturesItems = false;
            $lecturesTotal = 0;
        }

        if ($request->isXmlHttpRequest()) {
            $html = $this->render('ArmdLectureBundle:Default:plitka_one_wrap.html.twig', array(
                'lectures' => $lecturesItems,
            ));

            $response = new Response(json_encode(array(
                'html' => $html->getContent(),
                'total' => $lecturesTotal,
            )));
            $response->headers->set('Content-Type', 'application/json');

            return $response;

        } else {
            $superTypeList = $this->getDoctrine()->getManager()
                ->getRepository('ArmdLectureBundle:LectureSuperType')
                ->findAll();

            $genresList = $manager->getGenresBySupertype($superTypeId);

            return array(
                'superTypeId' => $superTypeId,
                'superTypeList' => $superTypeList,
                'genreId' => $genreId,
                'genresList' => $genresList,
                'lecturesTotal' => $lecturesTotal,
                'lectures' => $lecturesItems,
            );
        }
    }

    /**
     * @Route("/related")
     * @Template("ArmdLectureBundle:Default:related_lectures.html.twig")
     */
    public function relatedLecturesAction($tags, $limit, $superTypeCode)
    {
        $lectures = $this->getLectureManager()->findObjects(
            array(
                LectureManager::CRITERIA_LIMIT => $limit,
                LectureManager::CRITERIA_TAGS => $tags,
                LectureManager::CRITERIA_SUPER_TYPE_CODES_OR => array($superTypeCode)
            )
        );

//        $superType = $this->getDoctrine()->getRepository('ArmdLectureBundle:LectureSuperType')
//            ->findOneByCode($superTypeCode);
//
//            $this->getDoctrine()->getRepository('ArmdLectureBundle:Lecture')
//            ->findRelated($tags, $limit, $superType);

        return array('lectures' => $lectures);
    }

    /**
     * @Route("/fetch-genres/{superTypeId}", requirements={"superTypeId"="\d+"}, options={"expose"=true}, defaults={"_format"="json"}, name="armd_lecture_default_fetchgenresbysupertype")
     */
    public function fetchGenresBySupertypeAction($superTypeId = null)
    {
        $manager = $this->get('armd_lecture.manager.lecture');
        $res = $manager->getGenresBySupertype($superTypeId);

        return $res;
    }

    /**
     * @return \Armd\LectureBundle\Entity\LectureManager
     */
    public function getLectureManager()
    {
        return $this->get('armd_lecture.manager.lecture');
    }

    /**
     * @return \Armd\TagBundle\Entity\TagManager
     */
    public function getTagManager()
    {
        return $this->get('fpn_tag.tag_manager');
    }

}

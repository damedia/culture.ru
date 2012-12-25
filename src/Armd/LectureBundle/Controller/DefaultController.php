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
        $lecture = $this->getDoctrine()->getManager()
            ->getRepository('ArmdLectureBundle:Lecture')->find($id);
        if(!$lecture) {
            throw $this->createNotFoundException('Lecture not found');
        }

        $manager = $this->get('armd_lecture.manager.lecture');
        $rolesPersons = $manager->getStructuredRolesPersons($lecture);

        return $this->render('ArmdLectureBundle:Default:lecture_details.html.twig', array(
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
     * @Route()
     * @Template()
     */
    public function listAction()
    {
        $request = $this->getRequest();

        // Форма фильтра
        $defaultValues = array(
            'sort'      => $request->get('id', 'date'),
            'supertype' => $request->get('supertype', null),
            'page'      => $request->get('page', 1),
            'perPage'   => $request->get('perPage', 20),
        );
        $form = $this->createFormBuilder($defaultValues)
            ->add('search',   'text', array(
                'required' => false
            ))
            ->add('supertype', 'entity', array(
                'class' => 'ArmdLectureBundle:LectureSuperType',
                'property' => 'name',
                'empty_value' => 'Категория',
                'required' => false,
            ))
            ->add('genre',  'entity', array(
                'class' => 'ArmdLectureBundle:LectureCategory',
                'property' => 'title',
                'empty_value' => 'Жанр',
                'required' => false,
                'query_builder' => function(\Doctrine\ORM\EntityRepository $repo) {
                    return $repo->createQueryBuilder('c')
                                ->where('c.lectureSuperType IS NULL')
                                ->orderBy('c.lft', 'ASC');
                }
            ))
            ->add('theme',  'entity', array(
                'class' => 'ArmdLectureBundle:LectureSuperType',
                'property' => 'name',
                'empty_value' => 'Тематика',
                'required' => false
            ))
            ->add('sort',   'choice', array(
                'choices' => array('id' => 'by Id', 'title' => 'by Title', 'date' => 'by Date'),
                'required' => true
            ))
            ->add('page',    'text', array('required' => false))
            ->add('perPage', 'text', array('required' => false))
            ->getForm();

        $superType = null;
        $page      = 1;
        $perPage   = 20;
        $sort      = 'date';
        $typeIds   = null;
        $categoryIds = null;
        $searchString = '';

        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($this->getRequest());

            if ($form->isValid()) {
                $filter = $form->getData();

                $superType = $filter['supertype'];
                $page      = $filter['page'];
                $perPage   = $filter['perPage'];
                $sort      = $filter['sort'];
                $typeIds   = null;
                $categoryIds = isset($filter['genre']) ? array($filter['genre']->getId()) : null;
                $searchString = $filter['search'];
            }

        }

        $manager = $this->get('armd_lecture.manager.lecture');

        $lectures = $manager->findFiltered($superType, $page, $perPage, $typeIds, $categoryIds, $sort, $searchString);

        return array(
            'form' => $form->createView(),
            'lectures' => $lectures,
        );
    }

}

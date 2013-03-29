<?php

namespace Armd\LectureBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\LectureBundle\Entity\LectureManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/home", name="armd_lecture_home")
     * @Template("ArmdLectureBundle:Default:homepage.html.twig")
     */
    public function lectureHomepageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $superTypes = $em->getRepository('ArmdLectureBundle:LectureSuperType')->findAll();

        $lecturesBySuperType = array();
        foreach ($superTypes as $superType) {
            $lectures = $em->getRepository('ArmdLectureBundle:Lecture')->findBy(
                array(
                    'lectureSuperType' => $superType,
                    'published' => true
                ),
                array(
                    'recommended' => 'DESC',
                    'createdAt' => 'DESC'
                ),
                4
            );

            $lecturesBySuperType[$superType->getCode()] = $lectures;
        }

        return array(
            'superTypes' => $superTypes,
            'lecturesBySuperType' => $lecturesBySuperType
        );
    }

    /**
     * @Route("/lecture/", name="armd_lecture_lecture_index")
     */
    public function lectureIndexAction()
    {
        return $this->forward('ArmdLectureBundle:Default:index', array(
            'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_LECTURE'
        ));
    }

    /**
     * @Route("/translation/", name="armd_lecture_translation_index")
     */
    public function translationIndexAction()
    {
        return $this->forward('ArmdLectureBundle:Default:index', array(
            'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION'
        ));
    }

    /**
     * @Route("/cinema/", name="armd_lecture_cinema_index")
     */
    public function cinemaIndexAction()
    {
        return $this->forward('ArmdLectureBundle:Default:index', array(
            'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_CINEMA'
        ));
    }

    /**
     * @Route("/top100/", name="armd_lecture_top100_index")
     */
    public function top100IndexAction()
    {
        return $this->forward(
            'ArmdLectureBundle:Default:index',
            array(
                'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_CINEMA',
                'cinema_top100' => 1
            )
        );
    }


    /**
     * @Route("/index/{lectureSuperTypeCode}/", name="armd_lecture_default_index", options={"expose": true})
     * @Template("ArmdLectureBundle:Default:index.html.twig")
     */
    public function indexAction($lectureSuperTypeCode)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $translator = $this->get('translator');

        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->getMenuUri($lectureSuperTypeCode, $request)
        );


        $lectureSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode($lectureSuperTypeCode);


        $specialCategories = array();
        $cinemaTop100 = $request->get('cinema_top100');
        if ($cinemaTop100) {
            $top100Category = $em->getRepository('ArmdLectureBundle:LectureCategory')
                        ->findOneBySystemSlug('CINEMA_TOP_100');

            $categories = $em->getRepository('ArmdLectureBundle:LectureCategory')
                ->findBy(array('parent' => $top100Category), array('title' => 'ASC'));

        } else {
            $categories = $this->getLectureManager()->getCategoriesBySuperType($lectureSuperType);

            $specialCategories = array();
            if ($lectureSuperTypeCode === 'LECTURE_SUPER_TYPE_CINEMA') {
                $top100Category = $em->getRepository('ArmdLectureBundle:LectureCategory')
                            ->findOneBySystemSlug('CINEMA_TOP_100');
                if ($top100Category) {
                    $top100Category = clone($top100Category);
                    $top100Category->setTitle($translator->trans('LECTURE_SUPER_TYPE_TOP100'));
                    $specialCategories[] = $top100Category;
                }
            }
        }

        return array(
            'lectureSuperType' => $lectureSuperType,
            'categories' => $categories,
            'specialCategories' => $specialCategories,
            'categoryId' => $this->getRequest()->get('category_id'),
            'searchQuery' => $this->getRequest()->get('search_query'),
            'cinemaTop100' => $this->getRequest()->get('cinema_top100')
        );

    }

    /**
     * @Route("/list/{lectureSuperTypeCode}/", name="armd_lecture_list", options={"expose"=true})
     * @Template("ArmdLectureBundle:Default:list.html.twig")
     */
    public function lectureListAction($lectureSuperTypeCode)
    {
        $request = $this->getRequest();
        $criteria = array(
            LectureManager::CRITERIA_SUPER_TYPE_CODES_OR => array($lectureSuperTypeCode)
        );

        if ($request->query->has('offset')) {
            $criteria[LectureManager::CRITERIA_OFFSET] = $request->get('offset');
        }

        if ($request->query->has('limit')) {
            $limit = $request->get('limit');
            $criteria[LectureManager::CRITERIA_LIMIT] = $limit > 100 ? 100 : $limit;
        }

        if ($request->query->has('category_id') || $request->query->has('sub_category_id')) {

            if ($request->query->has('sub_category_id')) {
                $categoryId = $request->get('sub_category_id');
            } elseif ($request->query->has('category_id')) {
                $categoryId = $request->get('category_id');
            }

            if ($categoryId > 0 ) {
                $criteria[LectureManager::CRITERIA_CATEGORY_ID_OR_PARENT_ID] = $categoryId;
            }
        }

        if ($request->query->has('search_query')) {
            $criteria[LectureManager::CRITERIA_SEARCH_STRING] = $request->get('search_query');
        }

        $sort = $request->query->get('sort_by');
        switch ($sort) {
            case 'popularity':
                $criteria[LectureManager::CRITERIA_ORDER_BY] = array('viewCount' => 'DESC');
                break;
            case 'title':
                $criteria[LectureManager::CRITERIA_ORDER_BY] = array('title' => 'ASC');
                break;
            default:
                // sort by date (default)
                $criteria[LectureManager::CRITERIA_ORDER_BY] = array('createdAt' => 'DESC');
        }

        $cinemaTop100 = $request->get('cinema_top100');
        if ($cinemaTop100) {
            $criteria[LectureManager::CRITERIA_IS_TOP_100_FILM] = true;
        }

        $lectures = $this->getLectureManager()->findObjects($criteria);
        return array(
            'lectures' => $lectures
        );
    }


    /**
     * @Route(
     *  "/categories/{lectureSuperTypeCode}/{parentId}",
     *  name="armd_lecture_categories",
     *  defaults={"parentId"=null},
     *  options={"expose": true}
     * )
    */
    public function getLectureCategoriesAction($lectureSuperTypeCode, $parentId = null) {
        $em = $this->getDoctrine()->getManager();
        $result = array();

        $lectureSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode($lectureSuperTypeCode);

        if ($lectureSuperType) {
            if ($parentId) {
                $parentCategory = $em->find('ArmdLectureBundle:LectureCategory', $parentId);
            } else {
                $parentCategory = null;
            }

            $categories = $this->getLectureManager()->getCategoriesBySuperType($lectureSuperType, $parentCategory);
            $result = array_map(
                function ($value) {
                    return array(
                        'id' => $value->getId(),
                        'title' => $value->getTitle(),
                    );
                },
                $categories
            );
        }
        return new JsonResponse($result);
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
        $em = $this->getDoctrine()->getManager();
        $lecture = $em->getRepository('ArmdLectureBundle:Lecture')->find($id);

        if(!$lecture || !$lecture->getPublished()) {
            throw $this->createNotFoundException('Lecture not found');
        }
        $this->getTagManager()->loadTagging($lecture);

        $lecture->addViewCount();
        $em->flush();

        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->getMenuUri($lecture->getLectureSuperType()->getCode())
        );

        $manager = $this->get('armd_lecture.manager.lecture');
        $rolesPersons = $manager->getStructuredRolesPersons($lecture);
        $categories = $manager->getCategoriesBySupertype($lecture->getLectureSuperType());

        return $this->render('ArmdLectureBundle:Default:lecture_details.html.twig', array(
            'referer' => $this->getRequest()->headers->get('referer'),
            'lecture' => $lecture,
            'categories' => $categories,
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
     * @Route("/related", name="armd_lecture_related_lectures")
     * @Template("ArmdLectureBundle:Default:related_lectures.html.twig")
     */
    public function relatedLecturesAction()
    {
        $request = $this->getRequest();
        $tags = $request->get('tags', array());
        $limit = $request->get('limit');
        $superTypeCode = $request->get('superTypeCode');
        $id = $request->get('id');

        $lectures = $this->getLectureManager()->findObjects(
            array(
                LectureManager::CRITERIA_LIMIT => $limit,
                LectureManager::CRITERIA_TAGS => $tags,
                LectureManager::CRITERIA_SUPER_TYPE_CODES_OR => array($superTypeCode),
                LectureManager::CRITERIA_NOT_IDS => array($id),
                LectureManager::CRITERIA_RANDOM => true,
            )
        );

        return array('lectures' => $lectures);
    }

    public function getMenuUri($lectureSuperTypeCode, $request)
    {
        $router = $this->get('router');

        switch ($lectureSuperTypeCode) {
            case 'LECTURE_SUPER_TYPE_LECTURE':
                $uri = $router->generate('armd_lecture_lecture_index');
                break;
            case 'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION':
                $uri = $router->generate('armd_lecture_translation_index');
                break;
            case 'LECTURE_SUPER_TYPE_CINEMA':
                if ($request->get('cinema_top100')) {
                    $uri = $router->generate('armd_lecture_top100_index');
                } else {
                    $uri = $router->generate('armd_lecture_cinema_index');
                }
                break;
            default:
                $uri = $router->generate('armd_lecture_cinema_index');
        }
        return $uri;
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

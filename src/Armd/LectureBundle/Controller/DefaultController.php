<?php

namespace Armd\LectureBundle\Controller;

use Armd\LectureBundle\Entity\LectureGenre;
use Armd\LectureBundle\Entity\LectureSuperType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\LectureBundle\Entity\LectureManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route(
     *  "/cinema/{genreSlug}",
     *  name="armd_lecture_cinema_index",
     *  defaults={"genreSlug"=null}
     * )
     *
     */
    public function cinemaIndexAction($genreSlug = null)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $genreIds = array();

        // first level genre
        if ($genreSlug) {
            $genre1 = $em->getRepository('ArmdLectureBundle:LectureGenre')
                ->findOneBySlug($genreSlug);
        }
        if (empty($genre1)) {
            $genre1 = $em->getRepository('ArmdLectureBundle:LectureGenre')
                ->findOneBySlug('feature-film');
        }

        if ($genre1) {
            $genreIds[] = $genre1->getId();
        }

        // second level slug
        $genre2Id = $request->get('genre_id');
        if ($genre2Id) {
            $genreIds[] = $genre2Id;
        }

        return $this->forward(
            'ArmdLectureBundle:Default:index',
            array(
                'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_CINEMA',
            ),
            array(
                'genre1_id' => is_object($genre1) ? $genre1->getId() : 0, // for breadcrumbs
                'genre_ids' => $genreIds,
                'first_letter' => $request->get('first_letter'),
                'sort_by' => $request->get('sort_by'),
                'offset' => $request->get('offset'),
                'search_query' => $request->get('search_query')
            )
        );
    }

    /**
     * @Route("/lecture/", name="armd_lecture_lecture_index")
     */
    public function lectureIndexAction()
    {
        $request = $this->getRequest();
        $genreIds = array();
        $genreId = $request->get('genre_id');
        if ($genreId) {
            $genreIds[] = $genreId;
        }

        return $this->forward(
            'ArmdLectureBundle:Default:index',
            array(
                'lectureSuperTypeCode' => 'LECTURE_SUPER_TYPE_LECTURE'
            ),
            array(
                'genre_ids' => $genreIds,
                'first_letter' => $request->get('first_letter'),
                'sort_by' => $request->get('sort_by'),
                'offset' => $request->get('offset'),
                'search_query' => $request->get('search_query')
            )
        );
    }
    
    /**
     * @Route("/press-centre/news-video/", name="armd_lecture_news_index")
     */
    public function newsIndexAction()
    {   
        $lectureSuperTypeCode = 'LECTURE_SUPER_TYPE_NEWS';
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $lectureSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode($lectureSuperTypeCode);

        if ($request->query->has('tag_id')) {
            $tag = $em->getRepository('ArmdTagBundle:Tag')->find($request->get('tag_id'));
        } else {
            $tag = false;
        }

        // for breadcrumbs
        if ($request->query->has('genre1_id')) {
            $genre1 = $em->getRepository('ArmdLectureBundle:LectureGenre')->find($request->get('genre1_id'));
        } else {
            $genre1 = null;
        }

        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri($this->getMenuUri($lectureSuperTypeCode, $request));
        
        $genreIds = array();
        $genreId = $this->getRequest()->get('genre_id');
        if ($genreId) {
            $genreIds[] = $genreId;
        }

        $templates = $this->getTemplates($lectureSuperType, $genre1);
        return $this->render(
            $templates['index_template'],
            array(
                'lectureSuperType' => $lectureSuperType,
                'genres' => $this->getGenres($lectureSuperType),
                'genre1' => $genre1,
                'selectedGenreIds' => $genreIds,
                'searchQuery' => $request->get('search_query'),
                'tag' => $tag,
                'filter' => $this->getFilter(),
                'lecture' => $em->getRepository('ArmdLectureBundle:Lecture')->findOneBy(array('isHeadline' => true))
            )
        );
    }


    /**
     * @Route("/lecture/index/{lectureSuperTypeCode}/", name="armd_lecture_default_index", options={"expose": true})
     */
    public function indexAction($lectureSuperTypeCode)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $lectureSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode($lectureSuperTypeCode);

        if ($request->query->has('tag_id')) {
            $tag = $em->getRepository('ArmdTagBundle:Tag')->find($request->get('tag_id'));
        } else {
            $tag = false;
        }

        // for breadcrumbs
        if ($request->query->has('genre1_id')) {
            $genre1 = $em->getRepository('ArmdLectureBundle:LectureGenre')
                ->find($request->get('genre1_id'));
        } else {
            $genre1 = null;
        }

        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->getMenuUri($lectureSuperTypeCode, $request)
        );

        $templates = $this->getTemplates($lectureSuperType, $genre1);
        return $this->render(
            $templates['index_template'],
            array(
                'lectureSuperType' => $lectureSuperType,
                'genres' => $this->getGenres($lectureSuperType),
                'genre1' => $genre1,
                'selectedGenreIds' => $request->get('genre_ids'),
                'searchQuery' => $request->get('search_query'),
                'tag' => $tag,
                'filter' => $this->getFilter()
            )
        );

    }

    /**
     * @Route("/lecture/list/{lectureSuperTypeCode}/", name="armd_lecture_list", options={"expose"=true})
     * @Template("ArmdLectureBundle:Default:list.html.twig")
     */
    public function lectureListAction($lectureSuperTypeCode)
    {
        $request = $this->getRequest();
        $criteria = $this->getListCriteria($lectureSuperTypeCode);
        $lectures = $this->getLectureManager()->findObjects($criteria);

        // for breadcrumbs
        if ($request->query->has('genre1_id')) {
            $genre1 = $this->getDoctrine()->getRepository('ArmdLectureBundle:LectureGenre')
                ->find($request->get('genre1_id'));
        } else {
            $genre1 = null;
        }

        if ($request->query->has('templateName')) {
            $template = 'ArmdLectureBundle:Default:' . $request->get('templateName') . '.html.twig';
        } else {
            $lectureSuperType = $this->getDoctrine()->getRepository('ArmdLectureBundle:LectureSuperType')
                ->findOneByCode($lectureSuperTypeCode);

            $templates = $this->getTemplates($lectureSuperType, $genre1);
            $template = $templates['list_template'];
        }

        return $this->render(
            $template,
            array(
                'lectures' => $lectures,
                'genre1' => $genre1
            )
        );
    }

    /**
     * @Route("/press-centre/news-video/list", name="armd_lecture_news_list")
     */
    public function newsListAction()
    {
        $lectureSuperTypeCode = 'LECTURE_SUPER_TYPE_NEWS';
        $request = $this->getRequest();
        $criteria = $this->getListCriteria($lectureSuperTypeCode);
        $criteria[LectureManager::CRITERIA_ORDER_BY] = array('createdAt' => 'DESC');
        $lectures = $this->getLectureManager()->findObjects($criteria);

        $lecturesByMonth = array();
        foreach ($lectures as $n) {
            $date = $n->getCreatedAt()->format('Y-m');
            if (!isset($lecturesByMonth[$date])) {
                $lecturesByMonth[$date] = array();
            }
            $lecturesByMonth[$date][] = $n;
        }
        
        // for breadcrumbs
        if ($request->query->has('genre1_id')) {
            $genre1 = $this->getDoctrine()->getRepository('ArmdLectureBundle:LectureGenre')
                ->find($request->get('genre1_id'));
        } else {
            $genre1 = null;
        }

        return $this->render(
            'ArmdLectureBundle:Default:list_news.html.twig',
            array(
                'lecturesByMonth' => $lecturesByMonth,
                'genre1' => $genre1
            )
        );
    }

    /**
     * View lecture details.
     * Version can be one of these: full, trailer
     *
     * @Route("/cinema/view/{id}/{version}", requirements={"id"="\d+"}, name="armd_lecture_view", defaults={"version" = "trailer"})
     *
     */
    public function lectureDetailsAction($id, $version)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $lecture = $em->getRepository('ArmdLectureBundle:Lecture')->find($id);

        if(!$lecture || !$lecture->getPublished()) {
            throw $this->createNotFoundException('Lecture not found');
        }
        $this->getTagManager()->loadTagging($lecture);

        $lecture->addViewCount();
        $em->flush();

        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->getMenuUri($lecture->getLectureSuperType()->getCode(), $this->getRequest())
        );

        $manager = $this->get('armd_lecture.manager.lecture');
        $rolesPersons = $manager->getStructuredRolesPersons($lecture);
        $lectureSuperType = $lecture->getLectureSuperType();

        // for breadcrumbs
        if ($request->query->has('genre1_id')) {
            $genre1 = $this->getDoctrine()->getRepository('ArmdLectureBundle:LectureGenre')
                ->find($request->get('genre1_id'));
        } else {
            $genre1 = $lecture->getGenreByLevel(1);
        }
        
        $template = $lectureSuperType->getCode() === 'LECTURE_SUPER_TYPE_NEWS' ? 'item_news' : 'lecture_details';
        
        return $this->render('ArmdLectureBundle:Default:'.$template.'.html.twig', array(
            'referer' => $request->headers->get('referer'),
            'lecture' => $lecture,
            'genres' => $this->getGenres($lectureSuperType),
            'genre1' => $genre1,
            'lectureSuperType' => $lectureSuperType,
            'lectureVersion' => $version,
            'lectureRolesPersons' => $rolesPersons,
        ));
    }

    /**
     * @Route("/lecture/last-lectures/{lectureSuperTypeCode}/{limit}")
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
     * @Route("/lecture/related", name="armd_lecture_related_lectures")
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
    
    /**
     * @Route("/lecture/related_new", name="armd_lecture_related_lectures_new")
     * @Template("ArmdLectureBundle:Default:related_lectures_new.html.twig")
     */
    public function relatedLecturesNewAction()
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

        return array('lectures' => $lectures, 'superTypeCode' => $superTypeCode);
    }

    public function getGenres(LectureSuperType $lectureSuperType) {
        $qb = $this->getDoctrine()->getManager()->getRepository('ArmdLectureBundle:LectureGenre')
            ->createQueryBuilder('g')
            ->where('g.lectureSuperType = :super_type')
            ->orderBy('g.title', 'ASC')
            ->setParameter('super_type', $lectureSuperType);

        if ($lectureSuperType->getCode() === 'LECTURE_SUPER_TYPE_CINEMA') {
            $qb->andWhere('g.level = 2');
        }

        $genres = $qb->getQuery()->getResult();

        return $genres;
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

    public function getTemplates(LectureSuperType $lectureSuperType, LectureGenre $genre1 = null)
    {
        $templateName = false;
        if ($genre1 && $genre1->getTemplate()) {
            $templateName = $genre1->getTemplate();
        } elseif ($lectureSuperType->getTemplate()) {
            $templateName = $lectureSuperType->getTemplate();
        }

        if ($templateName) {
            $indexTemplate = 'ArmdLectureBundle:Default:index_' . $templateName . '.html.twig';
            $listTemplate =  'ArmdLectureBundle:Default:list_' . $templateName . '.html.twig';
        } else {
            $indexTemplate = 'ArmdLectureBundle:Default:index.html.twig';
            $listTemplate =  'ArmdLectureBundle:Default:list.html.twig';
        }

        return array(
            'index_template' => $indexTemplate,
            'list_template' => $listTemplate
        );
    }

    public function getMenuUri($superTypeCode, Request $request)
    {
        $router = $this->get('router');

        if ($superTypeCode === 'LECTURE_SUPER_TYPE_CINEMA') {

            if ($request->query->has('genre1_id')) {
                $genre = $this->getDoctrine()->getRepository('ArmdLectureBundle:LectureGenre')
                    ->find($request->get('genre1_id'));

            } elseif ($request->query->has('id')) {
                $lecture = $this->getDoctrine()->getRepository('ArmdLectureBundle:Lecture')
                    ->find($request->get('id'));

                foreach ($lecture->getGenres() as $genre) {
                    if ($genre->getLevel() == 1) {
                        break;
                    }
                }
            }
            if (empty($genre)) {
                $uri = $router->generate('armd_lecture_cinema_index');
            } else {
                $uri = $router->generate('armd_lecture_cinema_index', array('genreSlug' => $genre->getSlug()));
            }

        } elseif ($superTypeCode === 'LECTURE_SUPER_TYPE_LECTURE') {
            $uri = $router->generate('armd_lecture_lecture_index');
        } elseif ($superTypeCode === 'LECTURE_SUPER_TYPE_NEWS') {
            $uri = $router->generate('armd_lecture_news_index');
        }

        return $uri;
    }


    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    public function getItemsSitemap($action = null, $params = array())
    {
        // MYTODO: fix according to genres
        $items = array();

        if ($action) {
            switch ($action) {
                case 'cinemaIndexAction': {
                    $lectureSuperTypeCode = 'LECTURE_SUPER_TYPE_CINEMA';

                    break;
                }

                case 'lectureIndexAction': {
                    $lectureSuperTypeCode = 'LECTURE_SUPER_TYPE_LECTURE';

                    break;
                }

                case 'translationIndex': {
                    $lectureSuperTypeCode = 'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION';

                    break;
                }

                case 'top100IndexAction': {
                    $lectureSuperTypeCode = 'LECTURE_SUPER_TYPE_CINEMA';
                    $cinemaTop100 = 1;

                    break;
                }
            }

            if (isset($lectureSuperTypeCode)) {
                $criteria = array(
                    LectureManager::CRITERIA_SUPER_TYPE_CODES_OR => array($lectureSuperTypeCode),
                    LectureManager::CRITERIA_ORDER_BY => array('createdAt' => 'DESC')
                );

                if (isset($cinemaTop100)) {
                    $criteria[LectureManager::CRITERIA_IS_TOP_100_FILM] = true;
                }

                if ($lectures = $this->getLectureManager()->findObjects($criteria)) {
                    foreach ($lectures as $l) {
                        $items[] = array(
                            'loc' => $this->generateUrl('armd_lecture_view', array('id' => $l->getId())),
                            'lastmod' => $l->getCreatedAt()
                        );
                    }
                }
            }
        }

        return $items;
    }

    protected function getFilter()
    {
        $request = $this->getRequest();
        $filter = array(
            'alphabet' => array(
                'А','Б','В','Г','Д','Е','Ё','Ж','З','И','К','Л','М','Н','О',
                'П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Э','Ю','Я'
            ));
        $filter['letter'] = ($letter = $request->get('first_letter')) && in_array($letter, $filter['alphabet']) ? $letter : false;
        $filter['sortBy'] = $request->get('sort_by');
        $filter['offset'] = (int)$request->get('offset');

        return $filter;
    }
    
    protected function getListCriteria($lectureSuperTypeCode)
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
                $criteria[LectureManager::CRITERIA_ORDER_BY] = array('viewCount' => 'DESC');
        }


        if ($request->query->has('first_letter')) {
            $criteria[LectureManager::CRITERIA_FIRST_LETTER] = $request->get('first_letter');
        }

        if ($request->query->has('tag_id')) {
            $criteria[LectureManager::CRITERIA_TAG_ID] = $request->get('tag_id');
        }

        if ($request->query->has('genre_ids')) {
            $criteria[LectureManager::CRITERIA_GENRE_IDS_AND] = $request->get('genre_ids');
        }

        if ($request->query->has('recommended')) {
            $criteria[LectureManager::CRITERIA_RECOMMENDED] = true;
        }

        if ($request->query->has('show_at_featured')) {
            $criteria[LectureManager::CRITERIA_SHOW_AT_FEATURED] = true;
        }

        if ($request->query->has('show_at_slider')) {
            $criteria[LectureManager::CRITERIA_SHOW_AT_SLIDER] = true;
        }

        if ($request->query->has('limit_slider_genre_ids')) {
            $criteria[LectureManager::CRITERIA_LIMIT_SLIDER_FOR_GENRE_IDS] = $request->get('limit_slider_genre_ids');
        }

        if ($request->query->has('limit_featured_genre_ids')) {
            $criteria[LectureManager::CRITERIA_LIMIT_FEATURED_FOR_GENRE_IDS] = $request->get('limit_featured_genre_ids');
        }

        return $criteria;
    }
}

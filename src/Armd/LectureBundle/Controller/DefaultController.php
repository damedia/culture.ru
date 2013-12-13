<?php

namespace Armd\LectureBundle\Controller;

use Armd\LectureBundle\Entity\LectureGenre;
use Armd\LectureBundle\Entity\LectureSuperType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\LectureBundle\Entity\LectureManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Armd\UserBundle\Entity\Favorites;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller {
    const PALETTE_COLOR_HEX = '#327BA7';

    private $palette_color = 'palette-color-4';
    private $palette_background = 'palette-background-4';
    private $palette_colored_box = 'palette-colored-box-4';
    private $palette_favoritesIcon = 'palette-favoritesIcon-4';
    private $palette_favoritesIconAdded = 'palette-favoritesIconAdded-4';

    private function getMoviesRootGenres() {
        $em = $this->getDoctrine()->getManager();
        $genres = $em->getRepository('ArmdLectureBundle:LectureGenre')
            ->createQueryBuilder('g')
            ->innerJoin('g.lectureSuperType', 'st')
            ->where('st.code = :super_type_code')
            ->andWhere('g.level = :level')
            ->orderBy('g.sortIndex',  'ASC')
            ->addOrderBy('g.id',  'ASC')
            ->setParameters(array('super_type_code' => 'LECTURE_SUPER_TYPE_CINEMA', 'level' => 1))
            ->getQuery()->getResult();

        return $genres;
    }

    /**
     * Movies -> Hub page
     *
     * @Route("/cinema/{genreSlug}", name="armd_lecture_cinema_index", defaults={"genreSlug"=null}, options={"expose"=true})
     * @Template("ArmdLectureBundle:Movies:index.html.twig")
     */
    public function cinemaIndexAction($genreSlug = null) {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $selectedGenreId = $request->get('selectedGenreId');
        $selectedTagId = $request->get('tag_id');
        $searchQuery = $request->get('search_query');

        if ($selectedTagId) {
            $tag = $em->getRepository('ArmdTagBundle:Tag')->findOneById($selectedTagId);
        }
        else {
            $tag = false;
        }

        $moviesRootGenres = $this->getMoviesRootGenres();

        if ($genreSlug) {
            $genreRepository = $em->getRepository('ArmdLectureBundle:LectureGenre');
            $genresForSections = $genreRepository->findBySlug($genreSlug);
        }
        else {
            $genresForSections = $moviesRootGenres;
        }

        $filter = $this->getFilter();

        /* TODO: Use GROUP BY and extract this to a method! */
        $totals = array();
        $lectureRepository = $em->getRepository('ArmdLectureBundle:Lecture');
        foreach ($genresForSections as $genre) {
            $qb = $lectureRepository->createQueryBuilder('g');
            $count = $qb
                ->select('COUNT(g)')
                ->innerJoin('g.lectureSuperType', 'st')
                ->innerJoin('g.genres', 'gen')
                ->where('st.code = :super_type_code')
                ->andWhere('gen.id = :genre')
                ->setParameters(array('super_type_code' => 'LECTURE_SUPER_TYPE_CINEMA', 'genre' => $genre->getId()))
                ->getQuery()->getSingleScalarResult();
            $totals[$genre->getSlug()] = $count;
        }

        return array(
            'palette_color' => $this->palette_color,
            'palette_color_hex' => DefaultController::PALETTE_COLOR_HEX,
            'current_genre' => $genreSlug,
            'moviesRootGenres' => $moviesRootGenres,
            'genresForSections' => $genresForSections,
            'tag' => $tag,
            'genres' => $this->getGenresForMovies(),
            'selectedGenreId' => $selectedGenreId,
            'filter' => $filter,
            'totals' => $totals,
            'searchQuery' => $searchQuery
        );
    }

    /**
     * Movies -> Hub page -> Genre section list
     *
     * @Route("/cinema/genre-section-list/{genreId}", name="armd_movies_genre_section_list", options={"expose"=true})
     * @Template("ArmdLectureBundle:Movies:genreSectionList.html.twig")
     */
    public function moviesGenreSectionListAction($genreId) {
        $request = $this->getRequest();
        $genreSlug = $request->get('genreSlug');
        $firstLetter = $request->get('firstLetter');
        $limit = $request->get('limit');
        $loadedIds = $request->get('loadedIds');
        $extra = $request->get('extra');
        $selectedGenreId = $request->get('selectedGenreId');
        $tagId = $request->get('tagId');
        $searchQuery = $request->get('searchQuery');

        $genreIds = array($genreId);

        if ($extra) {
            $limit++;
        }

        if ($selectedGenreId) {
            $genreIds[] = $selectedGenreId;
        }

        $criteria = $this->getListCriteria('LECTURE_SUPER_TYPE_CINEMA');
        $criteria[LectureManager::CRITERIA_GENRE_IDS_AND] = array_unique($genreIds);
        $criteria[LectureManager::CRITERIA_LIMIT] = $limit;
        $criteria[LectureManager::CRITERIA_RANDOM] = true;

        if ($firstLetter) {
            $criteria[LectureManager::CRITERIA_FIRST_LETTER] = $firstLetter;
        }

        if ($loadedIds) {
            $criteria[LectureManager::CRITERIA_NOT_IDS] = array_unique($loadedIds);
        }

        if ($tagId) {
            $criteria[LectureManager::CRITERIA_TAG_ID] = $tagId;
        }

        if ($searchQuery) {
            $criteria[LectureManager::CRITERIA_SEARCH_STRING] = $searchQuery;
        }

        $movies = $this->getLectureManager()->findObjects($criteria);

        return array(
            'genreSlug' => $genreSlug,
            'palette_colored_box' => $this->palette_colored_box,
            'movies' => $movies,
            'extra' => $extra
        );
    }

    /**
     * @Route("/movies-index-sidebar", name="armd_movies_index_sidebar", options={"expose"=true})
     * @Template("ArmdLectureBundle:Movies:indexSidebar.html.twig")
     */
    public function moviesIndexSidebarAction() {
        $repo = $this->getDoctrine()->getRepository('ArmdLectureBundle:Lecture');
        $featuredMovies = $repo->findCinemaForMainPage('', 5, 'recommend');

        return array('featured' => $featuredMovies);
    }

    /**
     * Controller action selector for different lectureSuperTypeCode values.
     *
     * @Route("/index-page-selector/{lectureSuperTypeId}", name="armd_index_page_selector", requirements={"lectureSuperTypeId" = "\d+"}, options={"expose"=true})
     */
    public function indexPageSelectorAction($lectureSuperTypeId) {
        $request = $this->get('request');

        $controllerAction = '';
        $routeParameters = $request->attributes->get('_route_params');
        $allGetParameters = $request->query->all();

        $lectureSuperType = $this->getDoctrine()->getRepository('ArmdLectureBundle:LectureSupertype')->find($lectureSuperTypeId);

        if (!$lectureSuperType) {
            throw $this->createNotFoundException('Type not found!');
        }

        $lectureSuperTypeCode = $lectureSuperType->getCode();

        switch ($lectureSuperTypeCode) {
            case 'LECTURE_SUPER_TYPE_LECTURE':
                $controllerAction = 'ArmdLectureBundle:Default:lectureIndex';
                break;
            case 'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION':
                //
                break;
            case 'LECTURE_SUPER_TYPE_CINEMA':
                $controllerAction = 'ArmdLectureBundle:Default:cinemaIndex';
                break;
            case 'LECTURE_SUPER_TYPE_NEWS':
                //
                break;
            default:
                throw $this->createNotFoundException('Index page route not found!');
        }

        return $this->forward($controllerAction, $routeParameters, $allGetParameters);
    }

    /**
     * @param $objects
     * @Template("ArmdLectureBundle:Lectures:sidebarIndexWidget.html.twig")
     * @return array
     */
    public function sidebarLinkedLecturesWidgetAction(array $objects) {
        return array('items' => $objects);
    }

    /**
     * @Route("/lecture/", name="armd_lecture_lecture_index")
     */
    public function lectureIndexAction() {
        $request = $this->getRequest();
        $genreId = $request->get('genre_id');
        $genreIds = array();

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
     * @Route("/lecture/index/{lectureSuperTypeCode}/", name="armd_lecture_default_index", options={"expose": true})
     */
    public function indexAction($lectureSuperTypeCode) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();

        $lectureSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')->findOneByCode($lectureSuperTypeCode);

        if ($request->query->has('tag_id')) {
            $tag = $em->getRepository('ArmdTagBundle:Tag')->find($request->get('tag_id'));
        }
        else {
            $tag = false;
        }

        // for breadcrumbs
        if ($request->query->has('genre1_id')) {
            $genre1 = $em->getRepository('ArmdLectureBundle:LectureGenre')->find($request->get('genre1_id'));
        }
        else {
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
    public function lectureListAction($lectureSuperTypeCode) {
        $request = $this->getRequest();
        $criteria = $this->getListCriteria($lectureSuperTypeCode);
        $lectures = $this->getLectureManager()->findObjects($criteria);

        // for breadcrumbs
        if ($request->query->has('genre1_id')) {
            $genre1 = $this->getDoctrine()->getRepository('ArmdLectureBundle:LectureGenre')->find($request->get('genre1_id'));
        }
        else {
            $genre1 = null;
        }

        if ($request->query->has('templateName')) {
            $template = 'ArmdLectureBundle:Default:' . $request->get('templateName') . '.html.twig';
        }
        else {
            $lectureSuperType = $this->getDoctrine()->getRepository('ArmdLectureBundle:LectureSuperType')->findOneByCode($lectureSuperTypeCode);
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
     * @Route("/cinema/view/{id}", requirements={"id"="\d+"}, name="armd_lecture_view")
     * @Template("ArmdLectureBundle:Default:videoItem.html.twig")
     */
    public function lectureDetailsAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ArmdLectureBundle:Lecture')->find($id);

        if (!$entity || !$entity->getPublished()) {
            throw $this->createNotFoundException('Video not found!');
        }

        $this->getTagManager()->loadTagging($entity);

        $entity->addViewCount();
        $em->flush();

        $commentsIntegrator = $this->get('armd_comments_integrator');
        $isCommentable = $commentsIntegrator->entityIsCommentable($entity);

        $favoritesManager = $this->get('armd_favorites_manager');
        $isInFavorites = $favoritesManager->entityIsInFavorites(Favorites::TYPE_LECTURE, $entity->getId());

        $genres = array();
        if ($entity->getLectureSuperType()->getCode() == 'LECTURE_SUPER_TYPE_CINEMA') {
            $genresForMoviesIds = array();
            $genresForMovies = $this->getGenresForMovies();
            foreach ($genresForMovies as $genre) {
                $genresForMoviesIds[] = $genre->getId();
            }

            foreach ($entity->getGenres() as $genre) {
                if (in_array($genre->getId(), $genresForMoviesIds)) {
                    $genres[] = $genre;
                }
            }
        }

        $tags = array();
        foreach ($entity->getTags() as $tag) {
            if (!$tag->getIsTechnical() and !preg_match('/^[a-z]\d+$/', $tag->getName())) {
                $tags[] = $tag;
            }
        }

        return array(
            'palette_color' => $this->palette_color,
            'palette_color_hex' => self::PALETTE_COLOR_HEX,
            'palette_background' => $this->palette_background,
            'isCommentable' => $isCommentable,
            'palette_favoritesIcon' => $this->palette_favoritesIcon,
            'palette_favoritesIconAdded' => $this->palette_favoritesIconAdded,
            'isInFavorites' => $isInFavorites,
            'entity' => $entity,
            'genres' => $genres,
            'tags' => $tags
        );
    }

    /**
     * @Route("/lecture/last-lectures/{lectureSuperTypeCode}/{limit}")
     */
    public function lastLecturesAction($lectureSuperTypeCode, $limit) {
        $em = $this->getDoctrine()->getManager();
        $lectureSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')->findOneBy(array('code' => $lectureSuperTypeCode));
        $lectures = $em->getRepository('ArmdLectureBundle:Lecture')->findLastAdded($lectureSuperType, $limit);

        return $this->render('ArmdLectureBundle:Default:last_lectures.html.twig', array(
            'lectures' => $lectures
        ));
    }

    /**
     * @Route("/lecture/related", name="armd_lecture_related_lectures")
     * @Template("ArmdLectureBundle:Default:related_lectures.html.twig")
     */
    public function relatedLecturesAction() {
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
     * @Route("/lecture/sidebar-related", name="armd_sidebar_lecture_related_lectures")
     * @Template("ArmdLectureBundle:Lectures:sidebar_related_lectures.html.twig")
     */
    public function sidebarRelatedLecturesAction() {
        $request = $this->getRequest();
        $tags = $request->get('tags', array());
        $limit = $request->get('limit');
        $superTypeCode = $request->get('superTypeCode');
        $id = $request->get('id');
        $headerText = $request->get('headerText');

        $criteria = array(
            LectureManager::CRITERIA_LIMIT => $limit,
            LectureManager::CRITERIA_TAGS => $tags,
            LectureManager::CRITERIA_SUPER_TYPE_CODES_OR => array($superTypeCode),
            LectureManager::CRITERIA_NOT_IDS => array($id),
            LectureManager::CRITERIA_RANDOM => true,
        );

        $lectures = $this->getLectureManager()->findObjects($criteria);

        return array(
            'lectures' => $lectures,
            'headerText' => $headerText,
            'superTypeCode' => $superTypeCode
        );
    }

    /**
     * @Route("/lecture/related_new", name="armd_lecture_related_lectures_new")
     * @Template("ArmdLectureBundle:Default:related_lectures_new.html.twig")
     */
    public function relatedLecturesNewAction() {
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

    public function getGenresForMovies() {
        $em = $this->getDoctrine()->getManager();
        $superTypeRepository = $em->getRepository('ArmdLectureBundle:LectureSuperType');
        $lectureSuperType = $superTypeRepository->findOneByCode('LECTURE_SUPER_TYPE_CINEMA');

        return $this->getGenres($lectureSuperType);
    }

    /**
     * @return \Armd\LectureBundle\Entity\LectureManager
     */
    public function getLectureManager() {
        return $this->get('armd_lecture.manager.lecture');
    }

    /**
     * @return \Armd\TagBundle\Entity\TagManager
     */
    public function getTagManager() {
        return $this->get('fpn_tag.tag_manager');
    }

    public function getTemplates(LectureSuperType $lectureSuperType, LectureGenre $genre1 = null) {
        $templateName = false;

        if ($genre1 && $genre1->getTemplate()) {
            $templateName = $genre1->getTemplate();
        }
        elseif ($lectureSuperType->getTemplate()) {
            $templateName = $lectureSuperType->getTemplate();
        }

        if ($templateName) {
            $indexTemplate = 'ArmdLectureBundle:Default:index_' . $templateName . '.html.twig';
            $listTemplate =  'ArmdLectureBundle:Default:list_' . $templateName . '.html.twig';
        }
        else {
            $indexTemplate = 'ArmdLectureBundle:Default:index.html.twig';
            $listTemplate =  'ArmdLectureBundle:Default:list.html.twig';
        }

        return array(
            'index_template' => $indexTemplate,
            'list_template' => $listTemplate
        );
    }

    public function getMenuUri($superTypeCode, Request $request) {
        $router = $this->get('router');

        if ($superTypeCode === 'LECTURE_SUPER_TYPE_CINEMA') {
            if ($request->query->has('genre1_id')) {
                $genre = $this->getDoctrine()->getRepository('ArmdLectureBundle:LectureGenre')->find($request->get('genre1_id'));
            }
            elseif ($request->query->has('id')) {
                $lecture = $this->getDoctrine()->getRepository('ArmdLectureBundle:Lecture')->find($request->get('id'));

                foreach ($lecture->getGenres() as $genre) {
                    if ($genre->getLevel() == 1) {
                        break;
                    }
                }
            }

            if (empty($genre)) {
                $uri = $router->generate('armd_lecture_cinema_index');
            }
            else {
                $uri = $router->generate('armd_lecture_cinema_index', array('genreSlug' => $genre->getSlug()));
            }
        }
        elseif ($superTypeCode === 'LECTURE_SUPER_TYPE_LECTURE') {
            $uri = $router->generate('armd_lecture_lecture_index');
        }
        elseif ($superTypeCode === 'LECTURE_SUPER_TYPE_NEWS') {
            $uri = $router->generate('armd_lecture_news_index');
        }

        return $uri;
    }

    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    public function getItemsSitemap($action = null, $params = array()) {
        // MYTODO: fix according to genres
        $items = array();

        if ($action) {
            switch ($action) {
                case 'cinemaIndexAction':
                    $lectureSuperTypeCode = 'LECTURE_SUPER_TYPE_CINEMA';
                    break;
                case 'lectureIndexAction':
                    $lectureSuperTypeCode = 'LECTURE_SUPER_TYPE_LECTURE';
                    break;
                case 'translationIndex':
                    $lectureSuperTypeCode = 'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION';
                    break;
                case 'top100IndexAction':
                    $lectureSuperTypeCode = 'LECTURE_SUPER_TYPE_CINEMA';
                    $cinemaTop100 = 1;
                    break;
                default:
                    //do nothing
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

    protected function getFilter() {
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

    protected function getListCriteria($lectureSuperTypeCode) {
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
            default: //sort by date
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

    /**
     * @param string $type
     * @param string $date
     * @Route("cinema-main/{type}/{date}", defaults={"date"=""}, name="armd_lecture_cinema_mainpage")
     * @return Response
     */
    public function mainpageWidgetAction($type = 'recommend', $date = '') {
        $repo = $this->getDoctrine()->getRepository('ArmdLectureBundle:Lecture');

        $lectures = array();
        $showRecommended = $repo->countCinemaForMainPage($date, 'recommend');
        $showForChildren = $repo->countCinemaForMainPage($date, 'children');

        if (!$this->getRequest()->isXmlHttpRequest() && $type == 'recommend' && !$showRecommended && $showForChildren) {
            $type = 'children';
        }

        if ($showRecommended || $showForChildren) {
            $lectures = $repo->findCinemaForMainPage($date, 1, $type);
        }

        $genres = array();
        $router = $this->get('router');
        foreach (current($lectures)->getFiltrableGenres() as $genre) {
            $routeParams = array('genreSlug' => null, 'genre_id' => $genre->getId());
            $genres[] = '<a href="'.$router->generate('armd_lecture_cinema_index', $routeParams).'">'.$genre->getTitle().'</a>';
        }
        $genreString = implode(', ', $genres);

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render('ArmdLectureBundle:Default:mainpageCinemaWidgetItem.html.twig', array(
                'lectures' => $lectures,
                'date' => $date,
                'showRecommended' => $showRecommended,
                'showForChildren' => $showForChildren,
                'genresString' => $genreString
            ));
        }
        else {
            return $this->render('ArmdLectureBundle:Default:mainpageCinemaWidget.html.twig', array(
                'lectures' => $lectures,
                'date' => $date,
                'showRecommended' => $showRecommended,
                'showForChildren' => $showForChildren,
                'genresString' => $genreString
            ));
        }
    }

    /**
     * @param string $type
     * @param string $date
     * @Route("lecture-main/{type}/{date}", name="armd_lecture_mainpage", defaults={"date"=""})
     * @return Response
     */
    public function mainpageLectureWidgetAction($type = 'recommend', $date = '') {
        $repo = $this->getDoctrine()->getRepository('ArmdLectureBundle:Lecture');
        $lectures = $repo->findForMainPage($date, 4, $type);

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render('ArmdLectureBundle:Default:mainpageWidgetItem.html.twig', array('lectures' => $lectures, 'date' => $date));
        }
        else {
            return $this->render('ArmdLectureBundle:Default:mainpageWidget.html.twig', array('lectures' => $lectures, 'date' => $date));
        }
    }
}
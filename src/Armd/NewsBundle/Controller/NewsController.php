<?php

namespace Armd\NewsBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Armd\NewsBundle\Entity\NewsManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\ListBundle\Controller\ListController;
use Armd\MkCommentBundle\Entity\Thread;
use DateTime;
use Armd\UserBundle\Entity\Favorites;
use Armd\UserBundle\Entity\User;
use Armd\NewsBundle\Entity\News;

class NewsController extends Controller {
    const DEFAULT_RELATED_LIMIT = 5;
    const PALETTE_COLOR_HEX = '#95BD59';

    private $palette_color = 'palette-color-1';
    private $palette_background = 'palette-background-1';
    private $palette_favoritesIcon = 'palette-favoritesIcon-1';
    private $palette_favoritesIconAdded = 'palette-favoritesIconAdded-1';

    /**
     * @param $date
     * @Template("ArmdNewsBundle:News:mainpageWidget.html.twig")
     * @return array
     */
    public function mainpageWidgetAction($date = '') {
        $items = $this->getNewsRepository()->findForMainPage($date, 5);

        return array('items' => $items);
    }

    /**
     * @Route("/news-sidebar-widget", name="armd_news_sidebar_index_widget", options={"expose"=true})
     * @Template("ArmdNewsBundle:NewsNew:sidebarIndexWidget.html.twig")
     */
    public function sidebarIndexWidgetAction() {
        return array();
    }

    /**
     * @param $objects
     * @Template("ArmdNewsBundle:NewsNew:sidebarLinkedNewsWidget.html.twig")
     * @return array
     */
    public function sidebarLinkedNewsWidgetAction(array $objects) {
        return array('items' => $objects);
    }

    /**
     * @param $entity
     * @Template("ArmdNewsBundle:NewsNew:sidebarItemWidget.html.twig")
     * @return array
     */
    public function sidebarItemWidgetAction(News $entity) {
        $this->getTagManager()->loadTagging($entity);
        $tags = $entity->getTags();

        $commonTags = array();
        $techTags = array();
        foreach ($tags as $tag) {
            if (preg_match('/^[a-zA-Z]\d+$/', $tag->getName())) { //is technical tag
                $techTags[] = $tag->getName();
            }
            else {
                $commonTags[] = $tag->getName();
            }
        }

        $linkedObjects = array();
        $linkedObjectsQuantity = 0;
        if (count($techTags) > 0) {
            foreach ($techTags as $tagName) {
                $entityType = $tagName[0];
                $entityId = substr($tagName, 1);

                switch ($entityType) {
                    case "s": //special project
                        $linkedObjects['special_projects'][] = $entityId;
                        break;

                    case "n": //news
                        if ($entityId != $entity->getId()) { //if it's not the same entity
                            $linkedObjects['news'][] = $entityId;
                        }
                        break;

                    case "o": //atlas object
                        $linkedObjects['atlas_objects'][] = $entityId;
                        break;

                    case "l": //lecture
                        $linkedObjects['lectures'][] = $entityId;
                        break;

                    default:
                        //do nothing with unknown technical tag!
                }
            }

            //special projects
            //

            //news
            if (isset($linkedObjects['news']) AND (count($linkedObjects['news']) > 0)) {
                $linkedNews = $this->getNewsRepository()->findBy(array('id' => $linkedObjects['news']));
                $newsCount = count($linkedNews);
                if ($newsCount > 0) {
                    $linkedObjects['news'] = $linkedNews;
                    $linkedObjectsQuantity += $newsCount;
                }
            }

            //atlas objects
            if (isset($linkedObjects['atlas_objects']) AND (count($linkedObjects['atlas_objects']) > 0)) {
                $linkedAtlasObjects = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object')->findBy(array('id' => $linkedObjects['atlas_objects']));
                $atlasObjectsCount = count($linkedAtlasObjects);
                if ($atlasObjectsCount > 0) {
                    $linkedObjects['atlas_objects'] = $linkedAtlasObjects;
                    $linkedObjectsQuantity += $atlasObjectsCount;
                }
            }

            //lectures
            if (isset($linkedObjects['lectures']) AND (count($linkedObjects['lectures']) > 0)) {
                $linkedLectures = $this->getDoctrine()->getRepository('ArmdLectureBundle:Lecture')->findBy(array('id' => $linkedObjects['lectures']));
                $lecturesCount = count($linkedLectures);
                if ($lecturesCount > 0) {
                    $linkedObjects['lectures'] = $linkedLectures;
                    $linkedObjectsQuantity += $lecturesCount;
                }
            }
        }

        $relatedNews = array();
        $relatedObjectsLimit = NewsController::DEFAULT_RELATED_LIMIT - $linkedObjectsQuantity;
        if ($relatedObjectsLimit > 0) {
            $relatedNews = $this->getNewsManager()->findObjects(array(
                NewsManager::CRITERIA_LIMIT => $relatedObjectsLimit,
                NewsManager::CRITERIA_TAGS => $entity->getTags(),
                NewsManager::CRITERIA_RANDOM => true,
                NewsManager::CRITERIA_NOT_IDS => array($entity->getId())
            ));
        }

        $tagsString = implode(', ', $commonTags);
        $techTagsString = implode(', ', $techTags);

        return array(
            'tagsString' => $tagsString,
            'techTagsString' => $techTagsString,
            'linkedObjects' => $linkedObjects,
            'relatedNews' => $relatedNews
        );
    }

    /**
     * @Route("/{category}", name="armd_news_index_by_category", options={"expose"=true}, requirements={"category" = "[a-z]+"}, defaults={"category" = null})
     * @Template("ArmdNewsBundle:NewsNew:index.html.twig")
     */
    function newsIndexAction($category = null) { //TODO: should be renamed to '$categorySlug'
        $request = $this->getRequest();
        $searchQuery = $request->get('search_query');

        $categoryRepository = $this->getDoctrine()->getRepository('ArmdNewsBundle:Category');
        $categories = $categoryRepository->findAll();

        return array(
            'category' => $category,
            'categories' => $categories,
            'currentCategory' => $category,
            'palette_color' => $this->palette_color,
            'palette_color_hex' => NewsController::PALETTE_COLOR_HEX,
            'searchQuery' => $searchQuery
        );
    }

    /**
     * @Route("/index-list/{category}", name="armd_news_index_list", options={"expose"=true}, requirements={"category" = "[a-z]+"}, defaults={"category" = null})
     * @Template("ArmdNewsBundle:NewsNew:indexList.html.twig")
     */
    function newsIndexListAction($category = null) {
        $request = $this->getRequest();

        $offset = $request->get('offset');
        $searchQuery = $request->get('searchQuery');

        //$category = $categorySlug ? $categoryRepository->findOneBySlug($categorySlug) : null;
        //$category = $category ? array($category) : array('news', 'interviews', 'reportages', 'articles'); //TODO: weird!

        //$newsByDate = array();

        //if ($request->query->has('to_date') || $request->query->has('from_date')) {
        //    $criteria = array(
        //        NewsManager::CRITERIA_LIMIT => 100
        //    );

        //    if ($request->query->has('from_date')) {
        //        $criteria[NewsManager::CRITERIA_NEWS_DATE_SINCE] = new \DateTime($request->get('from_date'));
        //    }
        //    if ($request->query->has('to_date')) {
        //        $criteria[NewsManager::CRITERIA_NEWS_DATE_TILL] = new \DateTime($request->get('to_date'));
        //    }

        //    $news = $newsManager->findObjects($criteria);
        //    $newsByDate = $this->getDateGroupedNews($category, $news);
        //}
        //else { //the default way
        //$firstLoadedDate = new \DateTime($request->get('first_loaded_date')); //at first get minimal date

        //if ($request->query->has('first_loaded_date')) {
        //    $firstLoadedDate->sub(new \DateInterval('P1D'))->setTime(0, 0);
        //}

        $criteria = array(
            NewsManager::CRITERIA_CATEGORY_SLUGS_OR => $category,
            //NewsManager::CRITERIA_NEWS_DATE_TILL => $firstLoadedDate,
            NewsManager::CRITERIA_LIMIT => 5,
            NewsManager::CRITERIA_OFFSET => $offset,
            NewsManager::CRITERIA_ORDER_BY => array('newsDate' => 'DESC')
        );

        if ($searchQuery) {
            $criteria[NewsManager::CRITERIA_SEARCH_STRING] = $searchQuery;
        }

        //if (count($news) > 0) {
        // this is low date
        //    $criteria[NewsManager::CRITERIA_NEWS_DATE_SINCE] = $news[count($news) - 1]->getNewsDate();

        // now get news
        //    unset($criteria[NewsManager::CRITERIA_LIMIT]);
        //    $news = $newsManager->findObjects($criteria);
        //    $newsByDate = $this->getDateGroupedNews($category, $news);
        //}
        //}

        $news = $this->getNewsManager()->findObjects($criteria);

        return array('news' => $news);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="armd_news_item_by_category", options={"expose"=true})
     * @Template("ArmdNewsBundle:NewsNew:item.html.twig")
     */
    function newsItemAction($id) {
        $categoryRepository = $this->getDoctrine()->getRepository('ArmdNewsBundle:Category');
        $categories = $categoryRepository->findAll();

        $entityManager = $this->getDoctrine()->getManager();
        $entity = $entityManager->getRepository('ArmdNewsBundle:News')->findOneBy(array('id' => $id, 'published' => true));
        if (!$entity) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $id));
        }
        $this->getTagManager()->loadTagging($entity);

        $user = $this->container->get('security.context')->getToken()->getUser();
        if ($user instanceof User) { //if you are logged in
            $favorite = $entityManager->getRepository('ArmdUserBundle:Favorites')->findBy(array(
                'user' => $user->getId(),
                'resourceType' => Favorites::TYPE_MEDIA,
                'resourceId' => $entity->getId()
            ));
        }
        else {
            $favorite = false;
        }

        return array(
            'entity' => $entity,
            'isFavored' => $favorite ? true : false,
            'categories' => $categories,
            'palette_color_hex' => NewsController::PALETTE_COLOR_HEX,
            'palette_color' => $this->palette_color,
            'palette_favoritesIcon' => $this->palette_favoritesIcon,
            'palette_favoritesIconAdded' => $this->palette_favoritesIconAdded,
            'palette_background' => $this->palette_background,
            'isCommentable' => $this->isCommentable($entity)
        );
    }

    private function isCommentable($entity) { //TODO: this method should be elsewhere!
        $interfaces = class_implements(get_class($entity));

        return (isset($interfaces['Armd\MkCommentBundle\Model\CommentableInterface'])) ? true : false;
    }

    /**
     * @return \Armd\TagBundle\Entity\TagManager
     */
    private function getTagManager() {
        return $this->get('fpn_tag.tag_manager');
    }

    /**
     * @return \Armd\NewsBundle\Entity\NewsManager
     */
    private function getNewsManager() {
        return $this->get('armd_news.manager.news');
    }

    /**
     * @return \Armd\NewsBundle\Repository\NewsRepository
     */
    private function getNewsRepository() {
        return $this->getDoctrine()->getRepository('ArmdNewsBundle:News');
    }

    /**
     * @Route(
     *  "/text-search-result",
     *  name="armd_news_text_search_result",
     *  options={"expose"=true}
     * )
     * @Template("ArmdNewsBundle:News:text_search_result.html.twig")
     */
    public function textSearchResultAction() {
        $request = $this->getRequest();

        $criteria = array(
            NewsManager::CRITERIA_SEARCH_STRING => $request->get('search_query'),
            NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array($request->get('category_slug')),
            NewsManager::CRITERIA_LIMIT => 20,
            NewsManager::CRITERIA_OFFSET => $request->get('offset')
        );

        $news = $this->getNewsManager()->findObjects($criteria);

        return array(
            'news' => $news
        );
    }

    /**
     * @Route("/two-column-news-list/{category}", name="armd_news_two_column_list", defaults={"category"=null}, options={"expose"=true})
     */
    function twoColumnNewsListAction($category = null, $limit = null) { //TODO: this thing is used by name alias and is now BROKEN!
        //
    }

    public function lastAnnounceAction() {
        $entity = $this->getDoctrine()->getManager()->getRepository('ArmdNewsBundle:News')->getLastAnnounce();

        return $this->render(
            'ArmdNewsBundle:Default:lastAnnounce.html.twig',
            array(
                'entity' => $entity
            )
        );
    }

    /**
     * @param int $count
     *
     * @return array
     */
    private function getNewsFeed($count = 30) {
        return $this->getNewsManager()->findObjects(
            array(
                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array('news', 'interviews', 'reportages', 'articles'),
                NewsManager::CRITERIA_LIMIT => $count
            )
        );
    }

    /**
     * @Route("/widget/", defaults={"_format"="js"}, name="armd_news_widget")
     */
    function widgetAction() {
        $count = $this->getRequest()->get('count', 3);

        // Validate...
        if (!in_array($count, array(1, 3, 5))) {
            $count = 3; // Default.
        }

        $news = $this->getNewsFeed($count);

        return $this->render('ArmdNewsBundle:News:widget.js.twig', array(
            'newsList' => $news
        ));
    }

    /**
     * @Route("/get-widget/", name="armd_news_get_widget")
     */
    function getWidgetAction() {
        return $this->render('ArmdNewsBundle:News:get_widget.html.twig');
    }

    /**
     * @Route("/rss/", defaults={"_format"="xml"}, name="armd_news_rss")
     */
    function rssAction() {
        $news = $this->getNewsFeed();

        return $this->render('ArmdNewsBundle:News:rss.xml.twig', array(
            'news' => $news
        ));
    }

    /**
     * @Route("/map/", name="armd_news_map")
     * @Template()
     */
    public function mapAction() {
        $em = $this->getDoctrine()->getManager();
        $regionsRepo = $em->getRepository('ArmdAtlasBundle:Region');
        $regions = $regionsRepo->findBy(array(), array('title'=>'ASC'));
        if (! $regions)
            throw new NotFoundHttpException("Regions not found");

        $categories = $this->getNewsManager()->getCategories();
        if (! $categories)
            throw new NotFoundHttpException("Categories not found");

        $lastNews = $this->getNewsManager()->findObjects(
            array(
                NewsManager::CRITERIA_LIMIT => 5
            )
        );
        $themes = $this->getNewsManager()->getThemes();
        if (! $themes)
            throw new NotFoundHttpException("Themes not found");

        $dateFrom = new \DateTime('-1 month');
        $dateTo   = new \DateTime('+13 month');
        $dateFromStr = $dateFrom->format('d.m.Y');
        $dateToStr   = $dateTo->format('d.m.Y');
        return array(
            'regions' => $regions,
            'categories' => $categories,
            'themes' => $themes,
            'lastNews' => $lastNews,
            'dateFrom' => $dateFromStr,
            'dateTo' => $dateToStr,
        );
    }

    /**
     * @Route("/map/filter", name="armd_news_filter", defaults={"_format"="json"})
     */
    public function filterAction() {
        try {
            $filter = $this->getRequest()->get('f');

            $criteria = array(
                NewsManager::CRITERIA_IS_ON_MAP => true,
                //NewsManager::CRITERIA_HAS_IMAGE => true,
                NewsManager::CRITERIA_EVENT_DATE_SINCE => empty($filter['date_from']) ? new DateTime : new DateTime($filter['date_from']),
                NewsManager::CRITERIA_EVENT_DATE_TILL  => empty($filter['date_to']) ? new DateTime : new DateTime($filter['date_to']),
            );

            if (!empty($filter['category'])) {
                $criteria[NewsManager::CRITERIA_CATEGORY_IDS_OR] = $filter['category'];
            }
            else {
                throw new \Exception('Выберите хотя бы один тип события.');
            }

            if (!empty($filter['theme']) && $filter['theme'][0] != '') {
                $criteria[NewsManager::CRITERIA_THEME_IDS_OR] = $filter['theme'];
            }

            $news = $this->getNewsManager()->findObjects($criteria);

            $data = array();
            foreach ($news as $article) {
                $imageUrl = $this->get('sonata.media.twig.extension')->path($article->getImage(), 'thumbnail');
                if ($article->getTheme()) {
                    $iconUrl = $this->get('sonata.media.twig.extension')->path($article->getTheme()->getIconMedia(), 'reference');
                }
                else {
                    $iconUrl = '';
                }
                $data[] = array(
                    'id' => $article->getId(),
                    'title' => $article->getTitle(),
                    //'dateFrom' => $row->getDate(),
                    //'dateTo' => $row->getEndDate(),
                    'lon' => $article->getLon(),
                    'lat' => $article->getLat(),
                    'imageUrl' => $imageUrl,
                    'iconUrl' => $iconUrl,
                    'categoryId' => $article->getCategory()->getId(),
                );
            }

            $filter['is_on_map'] = true;

            return array(
                'success' => true,
                'message' => 'OK',
                'result' => array(
                    'filter' => $filter,
                    'data' => $data,
                ),
            );
        }
        catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    /**
     * @Route("/map/balloon", name="armd_news_map_balloon")
     * @Template()
     */
    public function balloonAction() {
        try {
            $id = (int) $this->getRequest()->get('id');
            $entity = $this->getDoctrine()->getManager()->getRepository('ArmdNewsBundle:News')->find($id);
            return array('entity' => $entity);
        }
        catch (\Exception $e) {
            print $e->getMessage();
        }
    }

    /**
     * @Route("/billboard-slider/", name="armd_news_billboard_slider")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function billboardAction() {
        $newsManager = $this->getNewsManager();
        $entities = array();
        foreach ($newsManager->getCategories() as $category) {
            $news = $newsManager->findObjects(array(
                    NewsManager::CRITERIA_HAS_IMAGE => true,
                    NewsManager::CRITERIA_CATEGORY_IDS_OR => array($category->getId()),
                    NewsManager::CRITERIA_LIMIT => 1,
                    NewsManager::CRITERIA_ORDER_BY => array(
                        'showOnMain' => 'DESC',
                        'showOnMainOrd' => 'ASC',
                        'important' => 'DESC',
                        'newsDate' => 'DESC'
                    )
                ));

            if (!empty($news)) {
                $entities[] = $news[0];
            }
        }

        return $this->render('ArmdNewsBundle:News:billboard.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * @Route("/read-also-news/", name="armd_news_read_also_news")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function readAlsoNewsAction() {
        $request = $this->getRequest();
        $entityId = $request->get('id');
        $limit = $request->get('limit', 10);

        $entity = $this->getDoctrine()->getManager()->getRepository('ArmdNewsBundle:News')->find($entityId);

        $this->get('fpn_tag.tag_manager')->loadTagging($entity);

        $categories = array('news', 'articles', 'reportages', 'interviews');

//        $entities = array();
//        foreach ($categories as $category) {
//            $entitiesPart = $this->getNewsManager()->findObjects(
//                array(
//                    NewsManager::CRITERIA_LIMIT => $limit,
//                    NewsManager::CRITERIA_NOT_IDS => array($entity->getId()),
//                    NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array($category),
//                    NewsManager::CRITERIA_TAGS => $entity->getTags(),
//                    NewsManager::CRITERIA_ORDER_BY => array('newsDate' => 'DESC')
//                )
//            );
//            $entities = array_merge($entities, $entitiesPart);
//        }

        $entities = $this->getNewsManager()->findObjects(
            array(
                NewsManager::CRITERIA_LIMIT => $limit,
                NewsManager::CRITERIA_NOT_IDS => array($entity->getId()),
                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => $categories,
                NewsManager::CRITERIA_TAGS => $entity->getTags(),
                NewsManager::CRITERIA_ORDER_BY => array('newsDate' => 'DESC'),
                NewsManager::CRITERIA_TAGS_DONT_PAD_RESULT => true
            )
        );

        return $this->render('ArmdNewsBundle:News:read-also-news.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * @Route("/memorial-events/", name="armd_news_memorial_events")
     */
    function memorialEventsAction() {
        $news = $this->getNewsManager()->findObjects(
            array(
                NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array('memorials'),
                NewsManager::CRITERIA_MEMORIAL_DATE => new \DateTime(),
                NewsManager::CRITERIA_LIMIT => 10
            )
        );

        return $this->render('ArmdNewsBundle:News:memorials.html.twig', array(
            'entities'      => $news
        ));
    }

    /**
     * @param \Armd\MkCommentBundle\Entity\Thread $thread
     * @return \Armd\MkCommentBundle\Entity\Comment
     */
    protected function getComments(Thread $thread = null) {
        if (empty($thread)) {
            return null;
        } else {
            return $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);
        }
    }

    protected function getControllerName() {
        return 'ArmdNewsBundle:News';
    }

    public function getDateGroupedNews($categories, $news) {
        $newsManager = $this->getNewsManager();

        if (in_array('news', $categories) || 'news' === $categories) {
            $groupedNews = $newsManager->getNewsGroupedByNewsDate($news);
        } else {
            $groupedNews = $newsManager->getNewsGroupedByNewsMonth($news);
        }

        return $groupedNews;
    }

    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    public function getItemsSitemap($action = null, $params = array()) {
        $items = array();

        switch ($action) {
            case 'newsIndexAction': {
                isset($params['category']) or $params['category'] = null;

                $criteria = array(
                    NewsManager::CRITERIA_CATEGORY_SLUGS_OR => $params['category'],
                    NewsManager::CRITERIA_NEWS_DATE_TILL => new \DateTime()
                );

                if ($news = $this->getNewsManager()->findObjects($criteria)) {
                    foreach ($news as $n) {
                        $items[] = array(
                            'loc' => $this->generateUrl('armd_news_item_by_category', array(
                                'id' => $n->getId(),
                                'category' => $n->getCategory()->getSlug()
                            )),
                            'lastmod' => $n->getPublishedAt()
                        );
                    }
                }

                break;
            }
        }

        return $items;
    }
}
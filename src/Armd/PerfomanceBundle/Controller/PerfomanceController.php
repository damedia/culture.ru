<?php

namespace Armd\PerfomanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Armd\PerfomanceBundle\Entity\PerfomanceManager;
use Armd\PerfomanceBundle\Entity\PerfomanceGanre;
use Armd\UserBundle\Entity\UserManager;
use Armd\UserBundle\Entity\Favorites;
use Armd\PerfomanceBundle\Entity\PerfomanceReview;

class PerfomanceController extends Controller {
	static $count = 32;
	static $abc = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Э','Ю','Я');
    static $video_width = 610;
    static $video_width_fancybox = 640;

    const PALETTE_COLOR_HEX = '#327BA7';

    private $palette_color = 'palette-color-5';
    private $palette_color_hex = '#5E3878';
    private $palette_background = 'palette-background-5';
    private $palette_favoritesIcon = 'palette-favoritesIcon-5';
    private $palette_favoritesIconAdded = 'palette-favoritesIconAdded-5';

    /**
     * @Route("/", name="armd_perfomance_index")
     * @Template("ArmdPerfomanceBundle:Perfomance:index.html.twig")
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/list/{ganreId}/{theaterId}", name="armd_perfomance_list", defaults={"ganreId"=0, "theaterId"=0}, options={"expose"=true})
     * @Template("ArmdPerfomanceBundle:Perfomance:list.html.twig")
     */
    public function listAction($ganreId = 0, $theaterId = 0)
    {
    	$request = $this -> getRequest();
    	$criteria = array();

    	//жанр
        if ( $request->get('ganre_id') )
			$ganreId = $request->get('ganre_id');

        if ($ganreId)
    		$criteria[PerfomanceManager::CRITERIA_GANRE_IDS_OR] = array($ganreId);

        //театр
        if ($request->get('theater_id')) {
            $theaterId = $request->get('theater_id');
        }

        if ($theaterId) {
            $criteria[PerfomanceManager::CRITERIA_THEATER_IDS_OR] = array($theaterId);
        }

        //слово для поиска
        if ($request->query->has('search_query')) {
            $criteria[PerfomanceManager::CRITERIA_SEARCH_STRING] = $request->get('search_query');
        }

    	$list = $this -> getPerfomanceManager() -> findObjects(
    		array(
    			PerfomanceManager::CRITERIA_LIMIT => self::$count,
    			PerfomanceManager::CRITERIA_OFFSET => 0,
    			PerfomanceManager::CRITERIA_ORDER_BY => array('title' => 'ASC')
    		) + $criteria
    	);

		return array(
			'list' => $list,
			'load_count' => self::$count,
			'ganres' => $this -> getEntityManager() -> getRepository('\Armd\PerfomanceBundle\Entity\PerfomanceGanre') -> findAll(),
			'theaters' => $this->getEntityManager()->getRepository('\Armd\TheaterBundle\Entity\Theater')->findBy(array(), array('title' => 'ASC')),
            'ganreId' => $ganreId,
            'theaterId' => $theaterId,
			'searchQuery' => $request->get('search_query'),
			'abc' => self::$abc
		);
    }

    /**
     * @Route("/list_content/", name="armd_perfomance_list_content", options={"expose"=true})
     * @Template("ArmdPerfomanceBundle:Perfomance:list-content.html.twig")
     */
    public function listContentAction()
    {
    	$request = $this -> getRequest();
    	$criteria = array();

    	//сортировка
    	switch ($request -> get('sort_by')) {
    		case 'date':
    		default:
    			$order_by = array('createdAt' => 'DESC');
    			break;
    		case 'abc':
    			$order_by = array('title' => 'ASC');
    			break;
    		case 'popular':
    			$order_by = array('viewCount' => 'DESC');
    			break;
    	}

    	//жанр
        if ($request->query->has('ganre_id')) {

            if ( $request->get('ganre_id') ) {
                $criteria[PerfomanceManager::CRITERIA_GANRE_IDS_OR] = array($request->get('ganre_id'));
            }
        }

        //слово для поиска
        if ($request->query->has('search_query')) {
            $criteria[PerfomanceManager::CRITERIA_SEARCH_STRING] = $request->get('search_query');
        }

        //первая буква
        if ($request->query->has('first_letter')) {
            $criteria[PerfomanceManager::CRITERIA_FIRST_LETTER] = $request->get('first_letter');
        }

        //театр
        if ($request->query->has('theater_id')) {
            $criteria[PerfomanceManager::CRITERIA_THEATER_IDS_OR] = $request->get('theater_id');
        }

        $list = $this -> getPerfomanceManager() -> findObjects(
    		array(
    			PerfomanceManager::CRITERIA_LIMIT => $request->get('limit') ? $request->get('limit') : self::$count,
    			PerfomanceManager::CRITERIA_OFFSET => $request->get('offset') ? $request->get('offset') : 0,
    			PerfomanceManager::CRITERIA_ORDER_BY => $order_by
    		) + $criteria
    	);

		return array('list' => $list);
    }

    /**
     * @Route("/related/", name="armd_perfomance_list_related")
     * @Template("ArmdPerfomanceBundle:Perfomance:list-related.html.twig")
     */
    public function listRelatedAction()
    {
        $request = $this->getRequest();
        $tags = $request->get('tags', array());
        $limit = $request->get('limit');

        $id = $request->get('id');

        $list = $this->getPerfomanceManager()->findObjects(
            array(
                PerfomanceManager::CRITERIA_LIMIT => $limit,
                PerfomanceManager::CRITERIA_TAGS => $tags,
                PerfomanceManager::CRITERIA_NOT_IDS => array($id),
                PerfomanceManager::CRITERIA_RANDOM => true,
            )
        );

        return array('list' => $list);
    }

    /**
     *
     * @Route("/item/{id}/", requirements={"id"="\d+"}, name="armd_perfomance_item")
     * @Template("ArmdPerfomanceBundle:Perfomance:item.html.twig")
     */
    public function itemAction($id) {
        $em = $this->getEntityManager();
        $performancesRepository = $em->getRepository('ArmdPerfomanceBundle:Perfomance');

        $entity = $performancesRepository->find($id);

        if (!$entity || !$entity->getPublished()) {
            throw $this->createNotFoundException('Perfomance not found');
        }

        $this->getTagManager()->loadTagging($entity);

        $entity->addViewCount();
        $em->flush();

        $favoritesManager = $this->get('armd_favorites_manager');
        $isInFavorites = $favoritesManager->entityIsInFavorites(Favorites::TYPE_PERFORMANCE, $entity->getId());

        return array(
            'entity' => $entity,
            'palette_color' => $this->palette_color,
            'palette_color_hex' => $this->palette_color_hex,
            'palette_background' => $this->palette_background,
            'palette_favoritesIcon' => $this->palette_favoritesIcon,
            'palette_favoritesIconAdded' => $this->palette_favoritesIconAdded,
            'isInFavorites' => $isInFavorites
        );
    }

    /**
     * @Route("/item-video/{id}/{mode}/", requirements={"id"="\d+"}, name="armd_perfomance_item_video", defaults={"mode"="perfomance"}, options={"expose"=true})
     */
    public function itemVideoAction($id, $mode = 'perfomance') {
        $em = $this->getEntityManager();
        $entity = $em->getRepository('ArmdPerfomanceBundle:Perfomance')->find($id);

        if (!$entity || !$entity->getPublished()) {
            throw $this->createNotFoundException('Perfomance not found');
        }

        if ($mode == 'trailer' && !$entity->getMediaTrailerVideo()) {
        	throw $this->createNotFoundException('Video not found');
        }

        $media = ($mode == 'trailer') ? $entity->getMediaTrailerVideo() : $entity->getMediaPerfomanceVideo();

		$media_twig_extension = $this->get('sonata.media.twig.extension');
		$media_twig_extension->initRuntime($this->get('twig'));

        echo $media_twig_extension->media($media, 'reference', array('width' => 700, 'height' => 520));
		exit;
    }

    /**
     * @Route("/review/{perfomance_id}/", requirements={"perfomance_id"="\d+"}, name="armd_perfomance_review")
     * @Template("ArmdPerfomanceBundle:Perfomance:review.html.twig")
     */
    public function reviewAction($perfomance_id) {
        $em = $this->getEntityManager();
        $performancesRepository = $em->getRepository('ArmdPerfomanceBundle:Perfomance');
        $performance = $performancesRepository->find($perfomance_id);

        if (!$performance) {
            return;
        }

        $author = $this->getAuthUser();

        if ($author) {
            $request = $this->getRequest();

            $review = new PerfomanceReview();
            $review->setPerfomance($performance);
            $form = $this->getReviewForm($review);

            if ($request->isMethod('POST')) {
                $form->bind($request);

                if ($form->isValid()) {
                    $review->setAuthor($author);
                    $review->setCreatedAt(new \DateTime());
                    $review->setPublished(true);

                    $em->persist($review);
                    $em->flush();

                    $this->notify();

                    $review = new PerfomanceReview();
                    $review->setPerfomance($performance);

                    $form = $this->getReviewForm($review);
                }
            }
        }

        return array(
            'form' => isset($form) ? $form->createView() : null,
            'perfomance_id' => $perfomance_id,
            'palette_background' => $this->palette_background
        );
    }

    /**
     * @Route("/review/list/{perfomance_id}/", requirements={"perfomance_id"="\d+"}, name="armd_perfomance_review_list", options={"expose"=true})
     * @Template("ArmdPerfomanceBundle:Perfomance:review-list.html.twig")
     */
    public function reviewListAction($perfomance_id) {
        $em = $this->getEntityManager();
        $performancesRepository = $em->getRepository('ArmdPerfomanceBundle:Perfomance');
        $performance = $performancesRepository->find($perfomance_id);

        $reviews = $performance ? $this->getReviewList($performance) : array();

        return array(
            'reviews' => $reviews
        );
    }

    /**
     * @Route("/review/comment/{review_id}/", requirements={"review_id"="\d+"}, name="armd_perfomance_review_comment")
     * @Template("FOSCommentBundle:Thread:async.html.twig")
     */
    public function reviewCommentAction($review_id) {
        return array(
            'id' => $this->getRequest()->getLocale().'_perfomance_review_'.$review_id
        );
    }

    public function getReviewForm($review) {

        $form = $this->createFormBuilder($review)
            ->add('body', 'textarea', array('required' => true))
            ->add('perfomance_id', 'hidden', array('data' => $review -> getPerfomance() -> getId()))
            ->getForm();

        return $form;
    }


    /**
     * @return \Armd\PerfomanceBundle\Entity\PerfomanceManager
     */
    public function getEntityManager() {
    	return $this->getDoctrine()->getManager();
    }

    /**
     * @return \Armd\PerfomanceBundle\Entity\PerfomanceManager
     */
    public function getPerfomanceManager()
    {
        return $this->get('armd_perfomance.manager.perfomance');
    }

    /**
     * @return \Armd\UserBundle\Entity\UserManager
     */
    public function getUserManager()
    {
        return $this->get('fos_user.user_manager.default');
    }

    /**
     * @return \Armd\TagBundle\Entity\TagManager
     */
    public function getTagManager()
    {
        return $this->get('fpn_tag.tag_manager');
    }

	/**
    * Получить авторизованного пользователя
    */
    public function getAuthUser()
    {
        if ($user = $this->get('security.context')->getToken()->getUser()) {
            if (is_object($user))
                return $user;
        }
        return null;
    }

    /**
     * Список рецензий
     */
    public function getReviewList($perfomance) {
        $em = $this->getEntityManager();
        $performancesReviewsRepository = $em->getRepository('\Armd\PerfomanceBundle\Entity\PerfomanceReview');
        $list = $performancesReviewsRepository->findBy(
            array('perfomance' => $perfomance, 'published' => true),
            array('createdAt' => 'DESC')
        );

        if (count($list)) {
            foreach ($list as $review) {
                $review->setCommentCount($this->getReviewCommentCount($review));
            }
        }

        return $list;
    }

    /**
     *
     */
    public function getReviewCommentCount($review) {

        $id = $this -> getRequest() -> getLocale() . '_perfomance_review_' . $review -> getId();
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if ($thread) {
            $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

            return count($comments);
        }

        return 0;
    }

    /**
     *
     */
    public function notify() {

        $um = $this -> getUserManager();
        $moderatos = $um -> getModerators();


        if (count($moderatos)) {

            $to = array();
            foreach ($moderatos as $moderator)
                $to[$moderator -> getEmail()] = $moderator -> getUsername();

            $message = \Swift_Message::newInstance()
                ->setSubject('Опубликована рецензия')
                ->setFrom('noreplay@culture.ru')
                ->setTo($to)
                ->setBody(
                    $this->render('ArmdPerfomanceBundle:Perfomance:notify.html.twig')
                )
            ;

            $this->get('mailer')->send($message);

        }

    }

    /**
     *
     */
     static public function getViewFormat($width, $height, $fancybox=false)
     {
        $new_width = ($fancybox ? self::$video_width_fancybox : self::$video_width);

        $height = round($new_width/$width * $height);
        $width = $new_width;

        return array('width'=>$width, 'height'=>$height);
     }

    /**
     * @param string $type
     * @param string $date
     * @Route("perfomance-main/{type}/{date}",
     *  name="armd_perfomance_mainpage",
     *  defaults={"date"=""},
     *  options={"expose"=true}
     * )
     * @return Response
     */
    public function mainpageWidgetAction($type = 'recommend', $date = '')
    {
        $repo = $this->getPerfomanceRepository();
        $objects = $repo->findForMainPage($date, 5, $type);

        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->render(
                'ArmdPerfomanceBundle:Perfomance:mainpageWidgetItem.html.twig',
                array('objects' => $objects, 'date' => $date)
            );
        } else {
            return $this->render(
                'ArmdPerfomanceBundle:Perfomance:mainpageWidget.html.twig',
                array('objects' => $objects, 'date' => $date)
            );
        }
    }

    /**
     * @return \Armd\PerfomanceBundle\Repository\PerfomanceRepository
     */
    private function getPerfomanceRepository()
    {
        return $this->getDoctrine()->getRepository('ArmdPerfomanceBundle:Perfomance');
    }
}

<?php

namespace Armd\TheaterBundle\Controller;

use Armd\TheaterBundle\Admin\TheaterAdmin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\TheaterBundle\Entity\TheaterManager;
use Armd\PerfomanceBundle\Entity\PerfomanceManager;

class DefaultController extends Controller {
    static $limit = 24;
    private $palette_color = 'palette-color-5';

    const PALETTE_COLOR_HEX = '#5E3878';
    const PERFORMANCES_DEFAULT_LIST_LENGTH = 8;
    const THEATERS_DEFAULT_LIST_LENGTH = 9;

    protected function getTheaterOrders()
    {
        return array(
            'date' => array(
                'title' => 'по дате',
                'order' => array('createdAt' => 'DESC')
            ),
            'abc' => array(
                'title' => 'по алфавиту',
                'order' => array('title' => 'ASC')
            )
        );
    }

    /**
     * @Route("/hub/{category}", name="armd_theaters_hub", defaults={"category"=null}, options={"expose"=true})
     * @Template("ArmdTheaterBundle:Default:index.html.twig")
     */
    public function hubIndexAction($category) {
        $em = $this->getDoctrine()->getManager();
        $genresRepository = $em->getRepository('\Armd\PerfomanceBundle\Entity\PerfomanceGanre');
        $theatersRepository = $em->getRepository('\Armd\TheaterBundle\Entity\Theater');
        $performancesRepository = $em->getRepository('\Armd\PerfomanceBundle\Entity\Perfomance');

        $request = $request = $this->getRequest();
        $genreId = $request->get('genreId');
        $theaterId = $request->get('theaterId');

        $genres = $genresRepository->findAll();
        $theaters = $theatersRepository->getAllTheatersOrderedByTitleAsc();
        $performancesCount = $performancesRepository->getPerformancesCount();

        return array(
            'performancesGenres' => $genres,
            'performancesCount' => $performancesCount,
            'theatersCount' => count($theaters),
            'theaters' => $theaters,
            'currentCategory' => $category,
            'palette_color' => $this->palette_color,
            'palette_color_hex' => self::PALETTE_COLOR_HEX,
            'genreId' => $genreId,
            'theaterId' => $theaterId
        );
    }

    /**
     * @Route("/performances-list/", name="armd_performances_list", options={"expose"=true})
     * @Template("ArmdTheaterBundle:Default:performances_list.html.twig")
     */
    public function performancesListAction() {
        $request = $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $selectedIds = $request->get('loadedIds', array());
        $selectedTheater = $request->get('selectedTheater', 0);
        $selectedGenre = $request->get('selectedGenre', 0);

        $performancesRepository = $em->getRepository('\Armd\PerfomanceBundle\Entity\Perfomance');
        $performances = $performancesRepository->getRandomSet(self::PERFORMANCES_DEFAULT_LIST_LENGTH, $selectedIds, $selectedTheater, $selectedGenre);

        return array(
            'performances' => $performances
        );
    }

    /**
     * @Route("/theaters-list/", name="armd_theaters_list", options={"expose"=true})
     * @Template("ArmdTheaterBundle:Default:theaters_list.html.twig")
     */
    public function theatersListAction() {
        $limit = self::THEATERS_DEFAULT_LIST_LENGTH;
        $request = $request = $this->getRequest();
        $selectedIds = $request->get('loadedIds', array());
        $cityId = $request->get('cityId');
        $categoryId = $request->get('categoryId');

        if ($cityId || $categoryId) {
            $limit = 0;
        }

        /**
         * If we are using TheaterManager class (which extends ListManager) and adding CRITERIA_RANDOM option
         * we often end up with an error (NoResultException: 'No result was found for query although at least
         * one row was expected.'). I don't know why exactly it is but the problem is in the class - ListManager,
         * method - getRandomObjectsFromQueryBuilder(). Therefore we are using my own implementation (into
         * Repository classes).
         *
         * And it also works a lot faster! Yay!
         */

        $em = $this->getDoctrine()->getManager();
        $theaterRepository = $em->getRepository('\Armd\TheaterBundle\Entity\Theater');
        $objects = $theaterRepository->getRandomSet($limit, $selectedIds, $cityId, $categoryId);

        return array(
            'objects' => $objects
        );
    }

    /**
     * @Route("/list/{category}", name="armd_theater_list", requirements={"category"="\d+"}, defaults={"category"=0}, options={"expose"=true})
     * @Template("ArmdTheaterBundle:Default:theater_list.html.twig")
     */
    public function theaterListAction($category = 0)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $this->getRequest();

        if ( $request->get('category') )
			$category = $request->get('category');

	    $city = null;
        if ( $request->get('city') )
			$city = $request->get('city');

        return array(
            'orders' => $this->getTheaterOrders(),
            'limit' => self::$limit,
            'category' => $category,
            'city' => $city,
            'cityList' => $em->getRepository('ArmdAddressBundle:City')
                ->findBy(array(), array('title' => 'ASC')),
            'categoryList' => $em->getRepository('ArmdTheaterBundle:TheaterCategory')
                ->findBy(array(), array('title' => 'ASC')),
            'searchQuery' => $request->get('search_query')
        );
    }

    /**
     * @Route("/list-data/{offset}/{limit}",
     *      name="armd_theater_list_data",
     *      options={"expose"=true},
     *      defaults={"offset"="0", "limit"="0"}
     * )
     */
    public function theaterListDataAction($offset = 0, $limit = 0)
    {
        $orders = $this->getTheaterOrders();

        $request = $this->getRequest();
        $criteria = array();

        $order = $request->get('order');

        if (empty($order) || !isset($orders[$order])) {
            $order = 'abc';
        }

        $criteria[TheaterManager::CRITERIA_ORDER_BY] = $orders[$order]['order'];

        $category = $request->get('category', false);

        if ($category) {
            $criteria[TheaterManager::CRITERIA_CATEGORY_IDS_OR] = array($category);
        }

        $city = $request->get('city', false);

        if ($city) {
            $criteria[TheaterManager::CRITERIA_CITY_IDS_OR] = array($city);
        }

        $searchText = $request->get('search_text');

        if (!empty($searchText)) {
            $criteria[TheaterManager::CRITERIA_SEARCH_STRING] = $searchText;
        }

        $criteria[TheaterManager::CRITERIA_LIMIT] = $limit ? $limit : self::$limit;
        $criteria[TheaterManager::CRITERIA_OFFSET] = $offset;

        return $this->render(
            'ArmdTheaterBundle:Default:theater_list_data.html.twig',
            array(
                'objects' => $this->getTheaterManager()->findObjects($criteria),
            )
        );
    }

    /**
     * @Route("/item/{id}", name="armd_theater_item",
     *     requirements={"id"="\d+"}, defaults={"id"=0}
     * )
     * @Template("ArmdTheaterBundle:Default:theater_item.html.twig")
     */
    public function theaterItemAction($id)
    {
        $billboards = array();

        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_theater_list')
        );

        $object = $this->getTheaterManager()->getObject($id);

        if (!$object) {
            throw $this->createNotFoundException('Page not found');
        }

        $now = new \DateTime();
        $m1 = $now->format('Y-m-01');
        $m2 = $now->modify('+1 month')->format('Y-m-01');
        $m3 = $now->modify('+1 month')->format('Y-m-01');

        foreach ($object->getBillboards() as $b) {
            $now = new \DateTime();
            $bdf = $b->getDate()->format('Y-m-01');

            if ($bdf == $m1) {
                $billboards['m1'][] = $b;
            } elseif ($bdf == $m2) {
                $billboards['m2'][] = $b;
            } elseif ($bdf == $m3) {
                $billboards['m3'][] = $b;
            }
        }

        $em = $this->getDoctrine()->getManager();

        return array(
            'object' => $object,
            'billboards' => $billboards,
            'referer' => $this->getRequest()->headers->get('referer'),
            'cityList' => $em->getRepository('ArmdAddressBundle:City')
                ->findBy(array(), array('title' => 'ASC')),
            'categoryList' => $em->getRepository('ArmdTheaterBundle:TheaterCategory')
                ->findBy(array(), array('title' => 'ASC'))
        );
    }

    /**
     * @Route("/performance-list-data/", name="armd_theater_performance_list_data", options={"expose"=true})
     * @Template("ArmdTheaterBundle:Default:performance_list_data.html.twig")
     */
    public function performanceListDataAction()
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

        //театр
        if ($request->query->has('theater_id')) {
            $criteria[PerfomanceManager::CRITERIA_THEATER_IDS_OR] = $request->get('theater_id');
        }

        $list = $this -> getPerfomanceManager() -> findObjects(
    		array(
    			PerfomanceManager::CRITERIA_LIMIT => $request->get('limit') ? $request->get('limit') : 12,
    			PerfomanceManager::CRITERIA_OFFSET => $request->get('offset') ? $request->get('offset') : 0,
    			PerfomanceManager::CRITERIA_ORDER_BY => $order_by
    		) + $criteria
    	);

		return array('list' => $list);
    }

    /**
     * @return \Armd\TheaterBundle\Entity\TheaterManager
     */
    public function getTheaterManager()
    {
        return $this->get('armd_theater.manager.theater');
    }

    /**
     * @return \Armd\PerfomanceBundle\Entity\PerfomanceManager
     */
    public function getPerfomanceManager()
    {
        return $this->get('armd_perfomance.manager.perfomance');
    }
}

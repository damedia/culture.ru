<?php

namespace Armd\TheaterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\TheaterBundle\Entity\TheaterManager;

class DefaultController extends Controller
{
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
     * @Route("/list/{category}", name="armd_theater_list",
     *      requirements={"category"="\d+"}, defaults={"category"=0}
     * )
     * @Template("ArmdTheaterBundle:Default:theater_list.html.twig")
     */
    public function theaterListAction($category = 0)
    {
        $em = $this->getDoctrine()->getManager();
        
        return array(
            'orders' => $this->getTheaterOrders(),
            'category' => $category,
            'cityList' => $em->getRepository('ArmdAddressBundle:City')
                ->findBy(array(), array('title' => 'ASC')),
            'categoryList' => $em->getRepository('ArmdTheaterBundle:TheaterCategory')
                ->findBy(array(), array('title' => 'ASC'))
        );
    }
    
    /**
     * @Route("/list-data/{offset}/{limit}",
     *      name="armd_theater_list_data",
     *      options={"expose"=true},
     *      defaults={"offset"="0", "limit"="24"}
     * )
     */
    public function  theaterListDataAction($offset = 0, $limit = 24)
    {
        $orders = $this->getTheaterOrders();      
        
        $request = $this->getRequest();
        $criteria = array();
        
        $order = $request->get('order');
        
        if (empty($order) || !isset($orders[$order])) {
            $order = 'date';          
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

        $criteria[TheaterManager::CRITERIA_LIMIT] = $limit;
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
        
        return array(
            'object' => $object,
            'billboards' => $billboards,
            'referer' => $this->getRequest()->headers->get('referer')
        );
    }
    
    /**
     * @return \Armd\TheaterBundle\Entity\TheaterManager
     */
    public function getTheaterManager()
    {
        return $this->get('armd_theater.manager.theater');
    }
}

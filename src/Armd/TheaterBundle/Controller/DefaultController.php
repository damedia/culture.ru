<?php

namespace Armd\TheaterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\TheaterBundle\Entity\TheaterManager;

class DefaultController extends Controller
{
    protected function getTheaterOrderList()
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
     * @Route("/list", name="armd_theater_list")
     * @Template("ArmdTheaterBundle:Default:theater_list.html.twig")
     */
    public function theaterListAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        return array(
            'orders' => $this->getTheaterOrderList(),
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
        $orders = $this->getTheaterOrderList();      
        
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
     * @Route("/item/{id}", name="armd_theater_theater_item", 
     *  requirements={"id"="\d+"}, defaults={"id"=0}
     * )
     */
    public function theaterItemAction($id)
    {
        return $this->render('ArmdTheaterBundle:Default:theater_item.html.twig');
    }
    
    /**
     * @return \Armd\TheaterBundle\Entity\TheaterManager
     */
    public function getTheaterManager()
    {
        return $this->get('armd_theater.manager.theater');
    }
}

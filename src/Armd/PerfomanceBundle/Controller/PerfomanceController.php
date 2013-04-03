<?php

namespace Armd\PerfomanceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Armd\PerfomanceBundle\Entity\PerfomanceManager;
use Armd\PerfomanceBundle\Entity\PerfomanceGanre;

class PerfomanceController extends Controller
{
	static $count = 32;
	
    /**
     * @Route("/", name="armd_perfomance_index")
     * @Template("ArmdPerfomanceBundle:Perfomance:index.html.twig")
     */	
    public function indexAction()
    {

		return array();
    }
    
    /**
     * @Route("/list/{ganreId}", name="armd_perfomance_list", defaults={"ganreId"=null}, options={"expose"=true})
     * @Template("ArmdPerfomanceBundle:Perfomance:list.html.twig")
     */	
    public function listAction($ganreId = null)
    {
    	$request = $this -> getRequest();
    	$criteria = array();
    	
    	//жанр
        if ( $request->get('ganre_id') ) 
			$ganreId = $request->get('ganre_id');
    	
        if ($ganreId)
    		$criteria[PerfomanceManager::CRITERIA_GANRE_IDS_OR] = array($ganreId);
    		
        //слово для поиска
        if ($request->query->has('search_query')) {
            $criteria[PerfomanceManager::CRITERIA_SEARCH_STRING] = $request->get('search_query');
        }    		
    	
    	$list = $this -> getPerfomanceManager() -> findObjects(
    		array(
    			PerfomanceManager::CRITERIA_LIMIT => self::$count, 
    			PerfomanceManager::CRITERIA_OFFSET => 0, 
    			PerfomanceManager::CRITERIA_ORDER_BY => array('createdAt' => 'DESC')
    		) + $criteria
    	);
    	
		return array(
			'list' => $list, 
			'load_count' => self::$count, 
			'ganres' => $this -> getEntityManager() -> getRepository('\Armd\PerfomanceBundle\Entity\PerfomanceGanre') -> findAll(),
			'ganreId' => $ganreId,
			'searchQuery' => $request->get('search_query')
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

        $list = $this -> getPerfomanceManager() -> findObjects(
    		array(
    			PerfomanceManager::CRITERIA_LIMIT => self::$count, 
    			PerfomanceManager::CRITERIA_OFFSET => $request->get('offset') ? $request->get('offset') : 0, 
    			PerfomanceManager::CRITERIA_ORDER_BY => $order_by
    		) + $criteria
    	);
    	
		return array('list' => $list);
    }  

    /**
     *
     * @Route("/item/{id}/", requirements={"id"="\d+"}, name="armd_perfomance_item")
     * @Template("ArmdPerfomanceBundle:Perfomance:item.html.twig")
     */
    public function itemAction($id)
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository('ArmdPerfomanceBundle:Perfomance')->find($id);

        if(!$entity || !$entity->getPublished()) {
            throw $this->createNotFoundException('Perfomance not found');
        }
        $this->getTagManager()->loadTagging($entity);

        $entity->addViewCount();
        $em->flush();

        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_perfomance_list')
        );

        return array(
            'referer' => $this->getRequest()->headers->get('referer'),
            'entity' => $entity,
            'ganres' => $this -> getEntityManager() -> getRepository('\Armd\PerfomanceBundle\Entity\PerfomanceGanre') -> findAll()
        );
    }       
    
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
     * @return \Armd\TagBundle\Entity\TagManager
     */
    public function getTagManager()
    {
        return $this->get('fpn_tag.tag_manager');
    }    
}

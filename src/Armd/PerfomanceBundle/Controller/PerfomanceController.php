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
	static $abc = array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Э','Ю','Я');
	
    /**
     * @Route("/", name="armd_perfomance_index")
     * @Template("ArmdPerfomanceBundle:Perfomance:index.html.twig")
     */	
    public function indexAction()
    {

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
    			PerfomanceManager::CRITERIA_ORDER_BY => array('createdAt' => 'DESC')
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
            'entity' => $entity,
            'ganres' => $this -> getEntityManager() -> getRepository('\Armd\PerfomanceBundle\Entity\PerfomanceGanre') -> findAll(),
            'theaters' => $this->getEntityManager()->getRepository('\Armd\TheaterBundle\Entity\Theater')->findBy(array(), array('title' => 'ASC')),
        );
    }       
    
    /**
     *
     * @Route("/item-video/{id}/{mode}/", requirements={"id"="\d+"}, name="armd_perfomance_item_video", defaults={"mode"="perfomance"}, options={"expose"=true})
     */
    public function itemVideoAction($id, $mode='perfomance')
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository('ArmdPerfomanceBundle:Perfomance')->find($id);

        if(!$entity || !$entity->getPublished()) {
            throw $this->createNotFoundException('Perfomance not found');
        }
        
        if ($mode == 'trailer' && !$entity->getTrailerVideo()) {
        	throw $this->createNotFoundException('Video not found');
        }

		$tvigle_twig_extension = $this -> get('armd_tvigle_video.twig.tvigle_video_extension');
		$tvigle_twig_extension -> initRuntime($this -> get('twig'));
		echo $tvigle_twig_extension -> videoPlayerFunction(($mode == 'trailer' ? $entity->getTrailerVideo() : $entity->getPerfomanceVideo()), '100%', 506);
		exit();        

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
     * @return \Armd\TagBundle\Entity\TagManager
     */
    public function getTagManager()
    {
        return $this->get('fpn_tag.tag_manager');
    }    
}

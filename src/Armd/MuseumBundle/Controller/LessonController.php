<?php

namespace Armd\MuseumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\MuseumBundle\Entity\Lesson;
use Armd\MuseumBundle\Entity\LessonManager;

class LessonController extends Controller
{

	static $count = 1;
	
    /**
     * @Route("/lesson/list/", name="armd_lesson_list", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Lesson:list.html.twig")
     */		
	public function listAction() {

		return array(
			'load_count' => self::$count,
			'searchQuery' => $this -> getRequest() -> get('search_query'),
			'city_list' => $this -> getCityList(),
			'museum_list' => $this -> getMuseumList(),
			'education_list' => $this -> getEducationList(),
			'subject_list' => $this -> getSubjectList(),
			'skill_list' => $this -> getSkillList(),
			'lesson_city' => $this -> getRequest() -> get('lesson_city'),
			'lesson_museum' => $this -> getRequest() -> get('lesson_museum'),
			'lesson_education' => $this -> getRequest() -> get('lesson_education'),
			'lesson_subject' => $this -> getRequest() -> get('lesson_subject'),
			'lesson_skill' => $this -> getRequest() -> get('lesson_skill')
		);
	}
	

    /**
     * @Route("/lesson/list_content/", name="armd_lesson_list_content", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Lesson:list-content.html.twig")
     */	
    public function listContentAction()
    {
    	$request = $this -> getRequest();
    	$criteria = array();
    	
    	$order_by = array('createdAt' => 'DESC');
    	
        //слово для поиска
        if ($request->query->has('search_query')) {
            $criteria[LessonManager::CRITERIA_SEARCH_STRING] = $request->get('search_query');
        }    	
    	
    	//навыки 
        if ($request->query->has('lesson_skill')) {

            if ( $request->get('lesson_skill') ) {
                $criteria[LessonManager::CRITERIA_SKILL_IDS_OR] = array($request->get('lesson_skill'));
            }
        }  
        
    	//музей
        if ($request->query->has('lesson_museum')) {

            if ( $request->get('lesson_museum') ) {
                $criteria[LessonManager::CRITERIA_MUSEUM_ID] = array($request->get('lesson_museum'));
            }
        }          
        
    	//город
        if ($request->query->has('lesson_city')) {

            if ( $request->get('lesson_city') ) {
                $criteria[LessonManager::CRITERIA_CITY_ID] = array($request->get('lesson_city'));
            }
        }                  
        
    	//Образование
        if ($request->query->has('lesson_education')) {

            if ( $request->get('lesson_education') ) {
                $criteria[LessonManager::CRITERIA_EDUCATION_ID] = array($request->get('lesson_education'));
            }
        }     

    	//предмет
        if ($request->query->has('lesson_subject')) {

            if ( $request->get('lesson_subject') ) {
                $criteria[LessonManager::CRITERIA_SUBJECT_ID] = array($request->get('lesson_subject'));
            }
        }                         
            	
        $list = $this -> getLessonManager() -> findObjects(
    		array(
    			LessonManager::CRITERIA_LIMIT => self::$count, 
    			LessonManager::CRITERIA_OFFSET => $request->get('offset') ? $request->get('offset') : 0, 
    			LessonManager::CRITERIA_ORDER_BY => $order_by
    		) + $criteria
    	);
    	    	
		return array('list' => $list);
    	
    }  	
    
    /**
     *
     * @Route("/lesson/item/{id}/", requirements={"id"="\d+"}, name="armd_lesson_item")
     * @Route("/lesson/item/{id}/print", requirements={"id" = "\d+"}, defaults={"is_print"=true}, name="armd_lesson_item_print")
     * @Template("ArmdMuseumBundle:Lesson:item.html.twig")
     */
    public function itemAction($id, $is_print = false)
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository('ArmdMuseumBundle:Lesson')->find($id);

        if(!$entity || !$entity->getPublished()) {
            throw $this->createNotFoundException('Lesson not found');
        }
        $this->getTagManager()->loadTagging($entity);

        // fix menu
        /*$this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_lesson_list')
        );*/
        
        if ($is_print)
        	return $this -> render('ArmdMuseumBundle:Lesson:item-print.html.twig', array(
        		'entity' => $entity
        	));

        return array(
            'entity' => $entity,
			'city_list' => $this -> getCityList(),
			'museum_list' => $this -> getMuseumList(),
			'education_list' => $this -> getEducationList(),
			'subject_list' => $this -> getSubjectList(),
			'skill_list' => $this -> getSkillList()            
        );
    }     
    
    public function getMuseumList() {
    	
    	$qb = $this -> getEntityManager() -> createQueryBuilder();
		$qb -> select('e')
		   ->from('ArmdMuseumBundle:RealMuseum', 'e')
		   ->orderBy('e.title', 'ASC');    	
		   
    	return $qb -> getQuery() -> getResult();
    }
    
    public function getCityList() {
    	
    	$qb = $this -> getEntityManager() -> createQueryBuilder();
		$qb -> select('e')
		   ->from('ArmdAddressBundle:City', 'e')
		   ->orderBy('e.title', 'ASC');    	
		   
    	return $qb -> getQuery() -> getResult();
    }    
    
    public function getEducationList() {
    	
    	$qb = $this -> getEntityManager() -> createQueryBuilder();
		$qb -> select('e')
		   ->from('ArmdMuseumBundle:Education', 'e')
		   ->orderBy('e.title', 'ASC');    	
		   
    	return $qb -> getQuery() -> getResult();
    } 
    
    public function getSubjectList() {
    	
    	$qb = $this -> getEntityManager() -> createQueryBuilder();
		$qb -> select('e')
		   ->from('ArmdMuseumBundle:LessonSubject', 'e')
		   ->orderBy('e.title', 'ASC');    	
		   
    	return $qb -> getQuery() -> getResult();
    }    
    
    public function getSkillList() {
    	
    	$qb = $this -> getEntityManager() -> createQueryBuilder();
		$qb -> select('e')
		   ->from('ArmdMuseumBundle:LessonSkill', 'e')
		   ->orderBy('e.title', 'ASC');    	
		   
    	return $qb -> getQuery() -> getResult();
    }      
        
    /**
     * @return \Armd\PerfomanceBundle\Entity\PerfomanceManager
     */    
    public function getEntityManager() {
    	return $this->getDoctrine()->getManager();
    }
    
    /**
     * @return \Armd\MuseumBundle\Entity\LessonManager
     */
    public function getLessonManager()
    {
        return $this->get('armd_museum.manager.lesson');
    }    
    
    /**
     * @return \Armd\TagBundle\Entity\TagManager
     */
    public function getTagManager()
    {
        return $this->get('fpn_tag.tag_manager');
    }       
}
<?php

namespace Armd\MuseumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\MuseumBundle\Entity\Lesson;
use Armd\MuseumBundle\Entity\LessonManager;

class LessonController extends Controller
{

	static $count = 20;
	
    /**
     * @Route("/lesson/list/", name="armd_lesson_list", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Lesson:list.html.twig")
     */		
	public function listAction() {

	    $list = $this -> getLessonList(self::$count+1);
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
			'lesson_skill' => $this -> getRequest() -> get('lesson_skill'),
			'list_count' => count($list)
		);
	}
	

    /**
     * @Route("/lesson/list_content/", name="armd_lesson_list_content", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Lesson:list-content.html.twig")
     */	
    public function listContentAction()
    {
    	    	
		return array('list' => $this -> getLessonList(self::$count));
    	
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
    
    /**
    * @Route("/lesson/filter-adjust/", name="armd_lesson_filter_adjust", options={"expose"=true})
    *
    */
    public function filterAdjustAction() {
        
        $list = $this -> getLessonList();
        
        $res = array(
            'museum' => array(),
            'city' => array(),
            'education' => array(),
            'subject' => array(),
            'skill' => array()
        );
        
        if (count($list)) {
            
            foreach ($list as $item) {
                if ($item->getMuseum()) {
                    if (!isset($res['museum'][$item->getMuseum()->getId()]))
                        $res['museum'][$item->getMuseum()->getId()] = $item->getMuseum()->getId();
                }
                if ($item->getEducation()) {
                    if (!isset($res['education'][$item->getEducation()->getId()]))
                        $res['education'][$item->getEducation()->getId()] = $item->getEducation() -> getId();
                }                
                if ($item->getCity()) {
                    if (!isset($res['city'][$item->getCity()->getId()]))
                        $res['city'][$item->getCity()->getId()] = $item->getCity() -> getId();
                }                                
                if (count($item -> getSkills())) {
                    foreach ($item -> getSkills() as $skill) {
                        if (!isset($res['skill'][$skill->getId()]))
                            $res['skill'][$skill->getId()] = $skill -> getId();
                    }
                }
                if (count($item -> getSubjects())) {
                    foreach ($item -> getSubjects() as $subject) {
                        if (!isset($res['subject'][$subject->getId()]))
                            $res['subject'][$subject->getId()] = $subject -> getId();
                    }
                }
            }
        }
        
        echo json_encode($res);
        exit();
    }
    
    public function getLessonList($limit = null) {
        
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
        
    	//предмет
        if ($request->query->has('lesson_subject')) {

            if ( $request->get('lesson_subject') ) {
                $criteria[LessonManager::CRITERIA_SUBJECT_IDS_OR] = array($request->get('lesson_subject'));
            }
        }          
        
    	//музей
        if ($request->query->has('lesson_museum')) {

            if ( $request->get('lesson_museum') ) {
                $criteria[LessonManager::CRITERIA_MUSEUM_ID] = $request->get('lesson_museum');
            }
        }          
        
    	//город
        if ($request->query->has('lesson_city')) {

            if ( $request->get('lesson_city') ) {
                $criteria[LessonManager::CRITERIA_CITY_ID] = $request->get('lesson_city');
            }
        }                  
        
    	//Образование
        if ($request->query->has('lesson_education')) {

            if ( $request->get('lesson_education') ) {
                $criteria[LessonManager::CRITERIA_EDUCATION_ID] = $request->get('lesson_education');
            }
        }
        
        if ($limit) {
            $criteria[LessonManager::CRITERIA_LIMIT] = $limit;
        }

                 
            	
        $list = $this -> getLessonManager() -> findObjects(
    		array(
    			LessonManager::CRITERIA_OFFSET => $request->get('offset') ? $request->get('offset') : 0, 
    			LessonManager::CRITERIA_ORDER_BY => $order_by
    		) + $criteria
    	);      
    	
    	return $list;  
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
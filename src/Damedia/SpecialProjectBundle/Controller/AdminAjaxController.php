<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CoreController;

class AdminAjaxController extends CoreController {
	public static function getKnownEntity($name) {
		
		$known=array(
				'news'=>array('ArmdNewsBundle:News', 		'id', 'title'),
				'museum'=>array('ArmdMuseumBundle:Museum',  'id', 'title'),
				'realMuseum'=>array('ArmdMuseumBundle:RealMuseum', 'id',  'title'),
				'lecture'=>array('ArmdLectureBundle:Lecture',  'id', 'title'),
				'artObject'=>array('ArmdExhibitBundle:ArtObject',  'id', 'title'),
				'theater'=>array('ArmdTheaterBundle:Theater', 'id',  'title')
		);
		if (!isset($known[$name]))	
			return false;
		return $known[$name];
	}
	
    public function getTemplateBlocksFormAction() {
        $response = array('content' => '');
        $request = $this->get('request');
        $templateId = $request->request->get('templateId');

        if ($templateId != '') {
            $template = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Template')->find($templateId);
            $twigFileName = $template->getTwigFileName();

            $helper = $this->container->get('special_project_helper');
            $twigTemplatesPath = $helper->getTwigTemplatesPath($this->container->get('kernel'));
            $fileContent = file_get_contents($twigTemplatesPath.DIRECTORY_SEPARATOR.$twigFileName);
            $blocksArray = $helper->getBlockNamesFromString($fileContent);

            $formBuilder = $this->createFormBuilder();
            foreach ($blocksArray as $blockName) {
                $formBuilder->add($blockName, 'textarea',
                                  array('required' => false,
                                        'attr' => array('class' => 'createPage_blockTextarea')));
            }
            $form = $formBuilder->getForm();

            $response['content'] = $this->renderView('DamediaSpecialProjectBundle:Admin:pageAdmin_ajax_templateBlocksForm.html.twig',
                                                     array('twigFileName' => $twigFileName,
                                                           'form' => $form->createView()));
        }
        else {
            $response['content'] = 'Template ID has not been sent!';
        }

        return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
    }
    
    
    public function jsonListAction($search_for)
    {
    	
    	$request = $this->getRequest();
    	$limit = $request->get('limit', 20);
    	if ($limit > 100) {
    		$limit = 20;
    	}
    	$search_query = $request->get('q', false);
    	$entityDesc=self::getKnownEntity($search_for);
    	if (!$search_query || !$entityDesc) {
    		 throw new NotFoundHttpException("Query not found");
    	};
    	
    	$em = $this->getDoctrine()->getManager();
    	/*
    	 * FULL TEXT SEARCH sphynx need
    	$news = $this->getNewsManager()->findObjects(
    			array(
    					NewsManager::CRITERIA_SEARCH_STRING => $search_query, // $request->get('search_query'),
    					//	NewsManager::CRITERIA_CATEGORY_SLUGS_OR => array($request->get('category_slug')),
    					NewsManager::CRITERIA_LIMIT => $limit
    					// , NewsManager::CRITERIA_OFFSET => $request->get('offset'),
    			)
    	);
    	 
    	$result=array();
    	for($i=0;$i<count($news);$i++) {
    	$result[] = array('value'=>$news[$i]->getId(), 'text'=>$news[$i]->getTitle());
    	};
    
    	*/
    	//--- previous week stats
    	$qb = $em->createQueryBuilder();
    	 
    	$class = $em->getMetadataFactory()->getMetadataFor($entityDesc[0]);
    	
    	$idField = $class->getColumnName($entityDesc[1]);
    	$textField= $class->getColumnName($entityDesc[2]);
    	 
    	$qb->select('n.'.$idField.', n.'.$textField)
    	->from($entityDesc[0], 'n')
    	->where($qb->expr()->like('n.'.$textField, $qb->expr()->literal('%'.$search_query.'%') )
    			//':srch') ) // .'=:srch') // %:srch%
    	)->setMaxResults( $limit );// ->setParameters(array('srch' => $search_query,));
    	$query = $qb->getQuery();
    
    	/*	   print_r(array(
    	 'sql'        => $query->getSQL(),
    			'parameters' => $query->getParameters(),
    	));
    	*/
    	$news=$query->getArrayResult();
    	 
    	// print_r($news);
    
    	 
    	$result=array();
    	for($i=0;$i<count($news);$i++) {
    		$result[] = array('value'=>$news[$i]['id'], 'label'=>$news[$i]['title']);
    	};
    	$response = new Response(json_encode($result));
    	$response->headers->set('Content-Type', 'application/json');
    	return $response;
    }
    
}
?>
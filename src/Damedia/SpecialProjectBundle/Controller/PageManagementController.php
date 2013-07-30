<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class PageManagementController extends Controller {
	public function getSnippetJsonlistAction() {
		$request = $this->get('request');
		$search_for = $request->get('entity');
    	$limit = $request->get('limit', 20);
    	if ($limit > 100) {
    		$limit = 20;
    	}
    	$search_query = $request->get('q', false);
    	$entityDesc = $this->getKnownEntity($search_for);
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
    	   ->where($qb->expr()->like('n.'.$textField, $qb->expr()->literal('%'.$search_query.'%'))
    			//':srch') ) // .'=:srch') // %:srch%
    	)->setMaxResults( $limit );// ->setParameters(array('srch' => $search_query,));
    	$query = $qb->getQuery();
    
    	/*	   print_r(array(
    	 'sql'        => $query->getSQL(),
    			'parameters' => $query->getParameters(),
    	));
    	*/
    	$news = $query->getArrayResult();
    	 
    	// print_r($news);
    
    	 
    	$result=array();
    	for($i=0;$i<count($news);$i++) {
    		$result[] = array('value'=>$news[$i]['id'], 'label'=>$news[$i]['title']);
    	};
    	
    	return $this->renderJson($result); //$response
	}
	
    public function getTinyAcFormAction() {
        return $this->render('DamediaSpecialProjectBundle:Admin:pageAdmin_iFrame_tinyAcForm.html.twig');
    }

    public function getTemplateBlocksFormAction() {
        $response = array('content' => '', 'errors' => '', 'buttons' => '');

        $request = $this->get('request');
        $templateId = (integer)$request->request->get('templateId');
        $pageId = (integer)$request->request->get('pageId');
        $page = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page')->find($pageId);

        if ($templateId == 0) {
        	$response['errors'] = 'Variable \'templateId\' is 0 or has not been sent!';
        	return $this->renderJson($response);
        }
        
        $template = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Template')->find($templateId);
        $twigFileName = $template->getTwigFileName();

        $helper = $this->container->get('special_project_helper');
        $twigTemplatesPath = $helper->getTwigTemplatesPath($this->container->get('kernel'));
        $fileContent = @file_get_contents($twigTemplatesPath.DIRECTORY_SEPARATOR.$twigFileName);
            
        if ($fileContent === false) {
            $response['errors'] = 'Template file \''.$twigFileName.'\' is missing!';
            return $this->renderJson($response);
        }

        $blocksPlaceholders = $helper->getBlockNamesFromString($fileContent);
        $blocks = $this->getBlocksForPageId($pageId);
        $chunks_mappedByPlaceholder = $this->getChunksForBlocks_mappedByPlaceholder($blocks);

        $formBuilder = $this->createFormBuilder();
        foreach ($blocksPlaceholders as $placeholder => $value) {
            $blockContent = $this->getBlockContentByPlaceholder($placeholder, $chunks_mappedByPlaceholder);

            $formBuilder->add($placeholder, 'textarea',
                              array('required' => false,
                                    'attr' => array('class' => 'createPage_blockTextarea'),
                                    'data' => $blockContent,
            						'label' => $placeholder));
        }
        $form = $formBuilder->getForm();

        $response['content'] = $this->renderView('DamediaSpecialProjectBundle:Admin:pageAdmin_formPart_templateBlocksForm.html.twig',
                                                 array('twigFileName' => $twigFileName,
                                                       'form' => $form->createView()));

        $response['buttons'] = $this->renderView('DamediaSpecialProjectBundle:Admin:pageAdmin_button_preview.html.twig',
                                                 array('admin' => $this->admin,
                                                       'object' => $page));

        return $this->renderJson($response);
    }

    public function previewPageAction() {
        $pageId = (integer)$this->getRequest()->get('id');
        $pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
        $page = $pageRepository->find($pageId);
        $helper = $this->get('special_project_helper');

        return $helper->renderSpecialProjectPage($this, $page, 'Страницы c ID <span class="variable">'.$pageId.'</span> не существует!');
    }



    private function getBlocksForPageId($pageId = null) {
        $result = array();

        if (!$pageId) {
            return $result;
        }

        return $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Block')->findByPage($pageId);
    }

    private function getChunksForBlocks_mappedByPlaceholder(array $blocks) {
        $result = array();
        $blocksMap = array();

        if (count($blocks) === 0) {
            return $result;
        }

        foreach ($blocks as $object) {
            $blocksMap[$object->getId()] = $object->getPlaceholder();
            $result[$object->getPlaceholder()] = array();
        }

        $chunks = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Chunk')->findBy(array('block' => array_keys($blocksMap)));

        foreach ($chunks as $object) {
            $placeholder = $blocksMap[$object->getBlock()->getId()];
            $result[$placeholder][] = $object;
        }

        return $result;
    }

    private function getBlockContentByPlaceholder($placeholder, array $chunks) {
        $result = '';

        if (!isset($chunks[$placeholder])) {
            return $result;
        }

        foreach ($chunks[$placeholder] as $object) {
            $result .= $object->getContent();
        }

        return $result;
    }
    
    
    
    private function getKnownEntity($name) {
    	$known = array('news' 		=> array('ArmdNewsBundle:News', 'id', 'title'),
    				   'museum' 	=> array('ArmdMuseumBundle:Museum', 'id', 'title'),
    				   'realMuseum' => array('ArmdMuseumBundle:RealMuseum', 'id', 'title'),
    				   'lecture'	=> array('ArmdLectureBundle:Lecture', 'id', 'title'),
    				   'artObject'	=> array('ArmdExhibitBundle:ArtObject', 'id', 'title'),
    				   'theater'	=> array('ArmdTheaterBundle:Theater', 'id', 'title'));
    	
    	return (isset($known[$name])) ? $known[$name] : false;
    }









    /*
    public function editPageAction($id) {
        $page = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page')->find($id);

        if (!$page) {
            throw $this->createNotFoundException('Page (id = "'.$id.'") not found!');
        }

        $template = $page->getTemplate();

        if (!$template) {
            throw $this->createNotFoundException('Page (id = "'.$id.'") has no Template!');
        }

        $pageBlocks = $this->getBlocksForPageId($page->getId());
        $renderedBlocks = $this->renderPageBlocksToEdit($pageBlocks);

        return $this->render('DamediaSpecialProjectBundle:Templates:'.$template->getTwigFileName(),
                             array('PageTitle' => $page->getTitle(),
                                   'Blocks' => $renderedBlocks));
    }
    */

    /*

    */

    /*
    private function renderPageBlocksToEdit(array $blocks) { //render blocks depending on settings
        $result = array();

        $chunks = $this->getChunksForBlocksArray($blocks);

        foreach ($blocks as $block) {
            $blockChunks = (isset($chunks[$block->getId()])) ? $chunks[$block->getId()] : array();
            $result[$block->getPlaceholder()] = $this->renderBlockContent($block->getPlaceholder(), $blockChunks);
        }

        return $result;
    }
    */

    /*

    */

    /*
    private function renderBlockContent($placeholder, array $blockChunks) {
        $result = '';

        if (count($blockChunks) == 0) {
            return '<textarea data-placeholder="'.$placeholder.'" class="editPage_blockContent"></textarea>';
        }

        foreach ($blockChunks as $chunk) {
            $result .= $chunk->getContent();
        }

        return $result;
    }
    */
}
?>
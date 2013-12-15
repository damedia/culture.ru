<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class PageManagementController extends Controller {
    public function getNewsJsonlistAction() {
        $request = $this->get('request');
        $searchPhrase = $request->get('term');
        $em = $this->getDoctrine()->getManager();

        $neighborsCommunicator = $this->container->get('special_project_neighbors_communicator');
        $json = $neighborsCommunicator->customSimpleSearch($em, 'news', $searchPhrase);

        return $this->renderJson($json);
    }

    public function getEntityViewslistAction() {
        $request = $this->get('request');
        $givenEntity = $request->get('entity', 'news');

        $neighborsCommunicator = $this->container->get('special_project_neighbors_communicator');
        $json = $neighborsCommunicator->createFriendlyEntityViewsList($givenEntity);

        return $this->renderJson($json);
    }

	public function getSnippetJsonlistAction() {
		$request = $this->get('request');
        $em = $this->getDoctrine()->getManager();

        $givenEntity = $request->get('entity', 'news');
        $searchPhrase = $request->get('q', false);
    	$limit = $request->get('limit', 20);
    	if ($limit > 100) {
    		$limit = 20;
    	}

        $neighborsCommunicator = $this->container->get('special_project_neighbors_communicator');
        $json = $neighborsCommunicator->createFriendlyEntityAutocompleteList($em, $givenEntity, $searchPhrase, $limit);

    	return $this->renderJson($json);
	}



    public function getTinyMediaFormAction() {
    	$mpool= $this->container->get('sonata.media.pool');
    	$contexts = $mpool->getContexts();
    	/* uploading form from SonataMediaBundle docs
    	 // create the target object
    	$post = new Post();
    	// create the form
    	$builder = $this->createFormBuilder($post);
    	$builder->add('media', 'sonata_media_type', array(
    			'provider' => 'sonata.media.provider.youtube',
    			'context'  => 'default'
    	));
    	 
    	$form = $builder->getForm();
    	 
    	// bind and transform the media's binary content into real content
    	if ($request->getMethod() == 'POST') {
    	$form->bindRequest($request);
    	 
    	// do stuff ...
    	}
    	*/
    	return $this->render('DamediaSpecialProjectBundle:Admin:pageAdmin_tinyMediaForm.html.twig', array('contexts'=>$contexts));
    }
    
    protected function path($id, $format) {
    	return $this->getTtwigMediaExtension()->path($id, $format);
    }
    
    /**
     * @return TwigExtension
     */
    protected function getTtwigMediaExtension()
    {
    	$twigMediaExtension = $this->container->get('sonata.media.twig.extension');
    	$twigMediaExtension->initRuntime($this->get('twig'));
    
    	return $twigMediaExtension;
    }
    
    public function getImagesJsonAction() {
    	$request = $this->get('request');

        $givenContext = $request->get('context', 'default');
        $searchPhrase = $request->get('q', false);
        $limit = $request->get('limit', 50);
    	 
    	$em = $this->getDoctrine()->getManager();
    	$qb = $em->createQueryBuilder();

        $neighborsCommunicator = $this->container->get('special_project_neighbors_communicator');
    	$entityDescription = $neighborsCommunicator->getFriendlyEntityDescription('image');

	    if ($searchPhrase) {
	    	$qb->select('n.'.$entityDescription['idField'].' AS id, n.'.$entityDescription['nameField'].' AS name')
	    	   ->from($entityDescription['class'], 'n')
	    	   ->where($qb->expr()->andX($qb->expr()->like('n.'.$entityDescription['nameField'],
                                         $qb->expr()->literal('%'.$searchPhrase.'%'))),
                                         'n.'.$entityDescription['contextField'].'='.$qb->expr()->literal($givenContext))
	    	   ->orderBy('n.'.$entityDescription['updatedAtField'], 'DESC')
	    	   ->setMaxResults($limit);
        }
        else {
	    	$qb->select('n.'.$entityDescription['idField'].', n.'.$entityDescription['nameField'])
	    	   ->from($entityDescription['class'], 'n')
	    	   ->where('n.'.$entityDescription['contextField'].'='.$qb->expr()->literal($givenContext))
	    	   ->orderBy('n.'.$entityDescription['updatedAtField'], 'DESC')
	    	   ->setMaxResults($limit);
	    }
        $result = $qb->getQuery()->getArrayResult();

    	$json = array();
        foreach ($result as $row) {
            $json[] = array('id' => $row['id'],
                            'name' => $row['name'],
                            'url' => $this->path($row['name'], 'thumbnail'),
                            'fullsize' => $this->path($row['name'], 'big'));
        }

    	return $this->renderJson($json);
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

        $snippetParser = $this->container->get('special_project_snippet_parser');

        $formBuilder = $this->createFormBuilder();
        foreach ($blocksPlaceholders as $placeholder => $value) {
            $blockContent = $this->getBlockContentByPlaceholder($placeholder, $chunks_mappedByPlaceholder);

            $snippetParser->entities_to_html($blockContent);

            $formBuilder->add($placeholder, 'textarea',
                              array('required' => false,
                                    'attr' => array('class' => 'createPage_blockTextarea tinymce'),
                                    //                'data-theme' => 'sproject_snippets'),
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
}
?>
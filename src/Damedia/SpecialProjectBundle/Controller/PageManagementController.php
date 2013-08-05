<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Application\Sonata\MediaBundle\Entity\Media;
use Sonata\MediaBundle\Provider\Pool;


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
    	$qb = $em->createQueryBuilder();
    	 
    	$class = $em->getMetadataFactory()->getMetadataFor($entityDesc[0]);
    	
    	$idField = $class->getColumnName($entityDesc[1]);
    	$textField= $class->getColumnName($entityDesc[2]);
    	 
    	$qb->select('n.'.$idField.', n.'.$textField)
    	   ->from($entityDesc[0], 'n')
    	   ->where($qb->expr()->like('n.'.$textField, $qb->expr()->literal('%'.$search_query.'%'))
    	)->setMaxResults( $limit );
    	$query = $qb->getQuery();

    	$news = $query->getArrayResult();

    	$result=array();
    	for($i=0;$i<count($news);$i++) {
    		$result[] = array('value'=>$news[$i]['id'], 'label'=>$news[$i]['title']);
    	};
    	
    	return $this->renderJson($result);
	}
	
    public function getTinyAcFormAction() {
        return $this->render('DamediaSpecialProjectBundle:Admin:pageAdmin_iFrame_tinyAcForm.html.twig');
    }

    
    // admin/damedia/specialproject/page/getTinyMediaForm
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
    	$search_query = $request->get('q');
    	 
    	$context=$request->get('context');
    	$context=$context?$context:"default";
    	 
    	$limit = $request->get('limit');
    	$limit = $limit?$limit:50;
    	 
    	$em = $this->getDoctrine()->getManager();
    	$qb = $em->createQueryBuilder('n');
    
    	$entityDesc = $this->getKnownEntity('image');
    	if (!$search_query || !$entityDesc) {
    		throw new NotFoundHttpException("Query not found");
    	};
    	$class = $em->getMetadataFactory()->getMetadataFor($entityDesc[0]);
    	if (!$class) {
    		throw new NotFoundHttpException("Class not found");
    	};
    	 
    	$idField = $entityDesc[1]; // $class->getColumnName();
    	$textField= $entityDesc[2]; // $class->getColumnName();
    	$contextField= 'context'; // $class->getColumnName();
    	$orderField= "updatedAt"; //$class->getColumnName('updatedAt');
    	 
    	$qb->select('n.'.$idField.', n.'.$textField)
    	->from($entityDesc[0], 'n')
    	->where($qb->expr()->andX($qb->expr()->like('n.'.$textField, $qb->expr()->literal('%'.$search_query.'%')) ),
    			'n.'.$contextField."=".$qb->expr()->literal($context) )
    	->orderBy('n.'.$orderField, 'DESC')
    	->setMaxResults( $limit );
    	 
    	$query = $qb->getQuery();
    	 
    	$result = $query->getArrayResult();
    	/*
    	 $mediaManager = $this->container->get('sonata.media.manager.media');
    	$request = $this->get('request');
    	$result=$mediaManager->findBy(array('context'=>'news', 'providerName'=>'sonata.media.provider.image', 'name' => 'Выс'));
    	$json=array();
    	 
    	$i=0;
    	foreach($result as $media) {
    	$json[]=array('id'=>$media->getId(), 'url'=>$this->path($media->getId(), 'thumbnail'), 'name' => $media->getAdminTitle() ); // , name=>$media->getName());
    	$i++;
    	if ($i>50)
    		break;
    	};
    	*/
    	$json=array();
    	for($i=0;$i<count($result);$i++) {
    		$json[]=array('id'=>$result[$i][$idField], 'name' => $result[$i][$textField],
    				'url'=>$this->path($result[$i][$idField], 'thumbnail'),
    				'fullsize'=>$this->path($result[$i][$idField], 'big') ); // , name=>$media->getName());
    	};
    	return $this->renderJson($json); //$response
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
                                    'attr' => array('class' => 'createPage_blockTextarea tinymce',
                                                    'data-theme' => 'sproject_snippets'),
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
    				   'theater'	=> array('ArmdTheaterBundle:Theater', 'id', 'title'),
    				'image'        => array('Application\Sonata\MediaBundle\Entity\Media', 'id', 'name')
    	);
    	
    	return (isset($known[$name])) ? $known[$name] : false;
    }
}
?>
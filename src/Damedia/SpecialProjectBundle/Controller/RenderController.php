<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RenderController extends Controller {
    public function indexAction($slug) {
    	$pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
    	$pagesFound = $pageRepository->findBy(array('slug' => $slug, 'isPublished' => true));
    	
    	if (count($pagesFound) == 0) {
    		return $this->render('DamediaSpecialProjectBundle:Default:error.html.twig',
    						     array('error' => 'Страница <span class="variable">'.$slug.'</span> не опубликована или не существует!'));
    	}
    	
    	$page = $pagesFound[0];
    	$title = $page->getTitle();
    	$template = $page->getTemplate();
    	$twigFileName = $template->getTwigFileName();
    	
    	$helper = $this->container->get('special_project_helper');
    	$twigTemplatesPath = $helper->getTwigTemplatesPath($this->container->get('kernel'));
    	$fileContent = @file_get_contents($twigTemplatesPath.DIRECTORY_SEPARATOR.$twigFileName);
    	
    	if ($fileContent === false) {
    		return $this->render('DamediaSpecialProjectBundle:Default:error.html.twig',
    				array('error' => 'Не найден файл шаблона <span class="variable">'.$twigFileName.'</span> для страницы <span class="variable">'.$title.'</span>!'));
    	}
    	
    	$blocksPlaceholders = $helper->getBlockNamesFromString($fileContent);
    	foreach ($page->getBlocks() as $block) {
    		$placeholder = $block->getPlaceholder();
    		
    		if (!isset($blocksPlaceholders[$placeholder])) {
    			continue;
    		}
    		
    		foreach ($block->getChunks() as $chunk) {
    			$blocksPlaceholders[$placeholder] .= $chunk->getContent();
    		}
    	}
    	
    	return $this->render('DamediaSpecialProjectBundle:Templates:'.$twigFileName,
    			array('PageTitle' => $title,
    				  'Blocks' => $blocksPlaceholders));
    }
}
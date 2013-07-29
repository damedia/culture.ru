<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Damedia\SpecialProjectBundle\Exception\TemplateNotFoundException;

class RenderController extends Controller {
	public function footerMenuElementsAction() {
		$pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
    	$pages = $pageRepository->findBy(array('parent' => null, 'isPublished' => true), array('id' => 'ASC'));
    	
    	$menuElements = array();
    	foreach ($pages as $page) {
    		$menuElements[] = array('href' => $this->generateUrl('damedia_special_project_view', array('slug' => $page->getSlug())), 'caption' => $page->getTitle());
    	}
		
		return $this->render('DamediaSpecialProjectBundle:Default:footerMenu.html.twig', array('ProjectsFooterMenu' => $menuElements));
	}
	
	public function indexAction() {
		return $this->render('DamediaSpecialProjectBundle:Default:index.html.twig');
	}
	
    public function viewAction($slug) {
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

        $breadcrumbs = array();
    	$this->get('special_project_helper')->collectPageBreadcrumbs($this, $page, $breadcrumbs);
    	//duplication when adding array element
    	$breadcrumbs[] = array('href' => $this->generateUrl('damedia_special_project_view', array('slug' => $page->getSlug())), 'caption' => $page->getTitle(), 'selected' => true);
    	
    	$helper = $this->container->get('special_project_helper');
    	$twigTemplatesPath = $helper->getTwigTemplatesPath($this->container->get('kernel'));
    	$fileContent = @file_get_contents($twigTemplatesPath.DIRECTORY_SEPARATOR.$twigFileName);
    	
    	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    	// throw new TemplateNotFoundException;
    	// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    	
    	if ($fileContent === false) {
    		return $this->render('DamediaSpecialProjectBundle:Default:error.html.twig',
    				array('error' => 'Не найден файл шаблона <span class="variable">'.$twigFileName.'</span> для страницы <span class="variable">'.$title.'</span>!'));
    	}
    	
    	$twigService = $this->get('twig');
    	$blocksPlaceholders = $helper->getBlockNamesFromString($fileContent);
    	foreach ($page->getBlocks() as $block) {
    		$placeholder = $block->getPlaceholder();
    		
    		if (!isset($blocksPlaceholders[$placeholder])) {
    			continue;
    		}
    		
    		foreach ($block->getChunks() as $chunk) {
    			$blocksPlaceholders[$placeholder] .= $this->get('string_twig_loader')->render($twigService, $chunk->getContent());
    		}
    	}
    	
    	return $this->render('DamediaSpecialProjectBundle:Templates:'.$twigFileName,
    			array('PageTitle' => $title,
                      'Stylesheet' => $page->getStylesheet(),
                      'Javascript' => $page->getJavascript(),
    				  'Breadcrumbs' => $breadcrumbs,
    				  'Blocks' => $blocksPlaceholders));
    }
}
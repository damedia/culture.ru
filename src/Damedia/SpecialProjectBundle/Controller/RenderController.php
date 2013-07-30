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
        $page = $pageRepository->findOneBy(array('slug' => $slug, 'isPublished' => true));
        $helper = $this->get('special_project_helper');

        return $helper->renderSpecialProjectPage($this, $page, 'Страница <span class="variable">'.$slug.'</span> не опубликована или не существует!');
    }
}
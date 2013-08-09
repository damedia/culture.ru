<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

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
        $helper = $this->get('special_project_helper');
        $breadcrumbs = $helper->createInitialBreadcrumbsArray($this);

        $pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
        $pages = $pageRepository->findBy(array('isPublished' => true, 'parent' => null), array('id' => 'DESC'));

        $projects = array();
        foreach ($pages as $page) {
            $projects[] = array('href' => $this->generateUrl('damedia_special_project_view', array('slug' => $page->getSlug())),
                                'caption' => $page->getTitle(),
                                'padding' => 0);

            $children = $page->getChildren();
            if (count($children) > 0) {
                foreach ($children as $child) {
                    $projects[] = array('href' => $this->generateUrl('damedia_special_project_view', array('slug' => $child->getSlug())),
                        'caption' => $child->getTitle(),
                        'padding' => 1);
                }
            }
        }

		return $this->render('DamediaSpecialProjectBundle:Default:index.html.twig', array('PageTitle' => 'Спецпроекты',
                                                                                          'Breadcrumbs' => $breadcrumbs,
                                                                                          'Projects' => $projects));
	}

    public function viewAction($slug) {
    	$pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
        $page = $pageRepository->findOneBy(array('slug' => $slug, 'isPublished' => true));
        $helper = $this->get('special_project_helper');

        return $helper->renderSpecialProjectPage($this, $page, 'Страница <span class="variable">'.$slug.'</span> не опубликована или не существует!');
    }

    /**
     * @return Response
     */
    public function mainpageWidgetAction()
    {
        /** @var \Damedia\SpecialProjectBundle\Repository\PageRepository $pageRepository */
        $pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
        $projects = $pageRepository->findForMainPage(5);

        return $this->render('DamediaSpecialProjectBundle:Default:mainpageWidget.html.twig', array('projects' => $projects));
    }
}
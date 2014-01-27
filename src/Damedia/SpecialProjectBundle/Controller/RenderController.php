<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class RenderController extends Controller {
    const DEFAULT_LIST_LIMIT = 5;

    public function snippetAction($entity, $itemId, $view) { //rename to: renderSnippetAction()
        $communicator = $this->get('special_project_neighbors_communicator');
        $entityDescription = $communicator->getFriendlyEntityDescription($entity);
        $twigFile = $communicator->getFriendlyEntityTwig($entity, $view);

        $object = $this->getDoctrine()->getRepository($entityDescription['class'])->find($itemId);

        if (!$object) {
            return $this->render('DamediaSpecialProjectBundle:Neighbors:notExists.html.twig', array('entity' => $entity, 'itemId' => $itemId));
        }

        return $this->render($twigFile, array('object' => $object));
    }

    public function footerMenuElementsAction() {
		$pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
    	$pages = $pageRepository->findBy(array('parent' => null, 'isPublished' => true), array('id' => 'ASC'));

    	$menuElements = array();
    	foreach ($pages as $page) {
    		$menuElements[] = array('href' => $this->generateUrl('damedia_special_project_view', array('slug' => $page->getSlug())), 'caption' => $page->getTitle());
    	}

		return $this->render('DamediaSpecialProjectBundle:Default:footerMenu.html.twig', array('ProjectsFooterMenu' => $menuElements));
	}

    /**
     * @Route("/project", name="damedia_special_project_list", options={"expose"=true})
     * @Template("DamediaSpecialProjectBundle:Default:index.html.twig")
     */
	public function indexAction() {
        /*
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
        */

		return array(
            //'Breadcrumbs' => $breadcrumbs,
            //'Projects' => $projects
        );
	}

    /**
     * @Route("/project-service/get-list", name="damedia_sprojects_list", options={"expose"=true})
     * @Template("DamediaSpecialProjectBundle:Default:list.html.twig")
     */
    public function listAction() {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();
        $pagesRepository = $em->getRepository('\Damedia\SpecialProjectBundle\Entity\Page');

        $offset = $request->get('loadedItemsCount', false);

        $objects = $pagesRepository->findBy(
            array('isPublished' => true, 'parent' => null),
            array('id' => 'DESC') ,
            self::DEFAULT_LIST_LIMIT,
            $offset
        );

        return array(
            'objects' => $objects
        );
    }

    public function viewAction($slug) { //rename to: previewPageAction()
    	$pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
        $page = $pageRepository->findOneBy(array('slug' => $slug, 'isPublished' => true));
        $helper = $this->get('special_project_helper');

        return $helper->renderSpecialProjectPage($this, $page, 'Страница <span class="variable">'.$slug.'</span> не опубликована или не существует!');
    }

    /**
     * @param string $date
     * @return Response
     */
    public function mainpageWidgetAction($date = '')
    {
        /** @var \Damedia\SpecialProjectBundle\Repository\PageRepository $pageRepository */
        $pageRepository = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page');
        $projects = $pageRepository->findForMainPage($date, 5);

        return $this->render('DamediaSpecialProjectBundle:Default:mainpageWidget.html.twig', array('projects' => $projects));
    }
}
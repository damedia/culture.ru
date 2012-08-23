<?php

namespace Armd\AtlasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdminController extends Controller
{
    /**
     * @Route("/category_up/{id}", name="armd_atlas_admin_category_tree_up")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function upAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Category');
        $category = $repo->findOneById($id);
        if ($category->getParent()) {
            $repo->moveUp($category);
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Route("/category_down/{id}", name="armd_atlas_admin_category_tree_down")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function downAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Category');
        $category = $repo->findOneById($id);
        if ($category->getParent()) {
            $repo->moveDown($category);
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * @Route("/delete_category/{id}", name="armd_atlas_admin_category_delete")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function deleteCategoryAction()
    {
        return new Response('not implemented');
    }

    /**
     * @Route("/export_objects")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function exportObjects()
    {
        $objects = $this->getDoctrine()->getManager()->getRepository('ArmdAtlasBundle:Object')->findAll();
        $categories = $this->getDoctrine()->getManager()->getRepository('ArmdAtlasBundle:Category')->childrenHierarchy();

        $content = $this->renderView('ArmdAtlasBundle:Admin:export_objects.xml.twig', array(
            'objects' => $objects,
            'categories' => $categories
        ));

        $response = new Response($content, 200, array('content-type' => 'text/xml'));
//        $response = new Response($content, 200);
        return $response;
    }

    /**
     * @Route("/rebuild_tree")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function rebuildTreeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repairer = new \Armd\AtlasBundle\Util\TreeRepairer();
        $repairer->rebuildTree($em, $em->getRepository('ArmdAtlasBundle:Category'));
        return new Response('ok');
    }

    /**
     * @Route("/verify_tree")
     * @Secure(roles="ROLE_SUPER_ADMIN,ROLE_SONATA_ADMIN")
     */
    public function verifyTreeAction()
    {
        $errors = $this->getDoctrine()->getManager()
            ->getRepository('ArmdAtlasBundle:Category')->verify();
        var_dump($errors);
        return new Response();
    }


}
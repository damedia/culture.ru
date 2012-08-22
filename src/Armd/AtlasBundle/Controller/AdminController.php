<?php

namespace Armd\AtlasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}
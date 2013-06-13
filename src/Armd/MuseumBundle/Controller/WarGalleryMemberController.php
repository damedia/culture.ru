<?php

namespace Armd\MuseumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class WarGalleryMemberController extends Controller
{
    /**
     * @var int $itemPerPage
     */
    protected $itemsPerPage = 24;

    /**
     * @Route("/war-gallery/{page}", name="armd_war_gallery", requirements={"page"="\d+"}, defaults={"page"=1}, options={"expose"=true})
     * @Template("ArmdMuseumBundle:WarGalleryMember:list.html.twig")
     */     
    public function listAction($page)
    {
        $qb = $this->getDoctrine()
            ->getRepository('ArmdMuseumBundle:WarGalleryMember')
                ->createQueryBuilder('wgm');

        $qb
            ->where('wgm.published = true')
            ->orderBy('wgm.name', 'asc');

        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate($qb, $page, $this->itemsPerPage, array('distinct' => false));

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render(
                'ArmdMuseumBundle:WarGalleryMember:list.ajax.html.twig',
                array('pagination' => $pagination)
            );
        }

        return array(
            'pagination' => $pagination
        );
    }

    /**
     * @Route("/war-gallery-member/{id}", name="armd_war_gallery_member", requirements={"id"="\d+"})
     * @Route("/war-gallery-member/{id}/print", name="armd_war_gallery_member_print", requirements={"id"="\d+"}, defaults={"print"=true})
     */     
    public function itemAction($id, $print = false)
    {
        $entity = $this->getDoctrine()->getRepository('ArmdMuseumBundle:WarGalleryMember')->find($id);

        if (null === $entity) {
            throw $this->createNotFoundException("Unable to find record {$id}");
        }

        return $this->render(
            'ArmdMuseumBundle:WarGalleryMember:item.' .($print ? 'print.' : '') .'html.twig',
            array('entity' => $entity)
        );
    }
}

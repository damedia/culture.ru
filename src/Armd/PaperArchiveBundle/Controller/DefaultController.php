<?php

namespace Armd\PaperArchiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/press-centre/editions/{slug}/", name="armd_paper_edition")
     * @Template()
     */
    public function indexAction($slug=null)
    {
        //      * Route("/press-centre/archive/", name="armd_paper_archive")

        $edition = $this->getDoctrine()->getRepository('ArmdPaperArchiveBundle:PaperEdition')->findOneBy(
            array('slug' => $slug)
        );
        $items = $this->getDoctrine()->getRepository('ArmdPaperArchiveBundle:PaperArchive')->findBy(
            array('edition' => $edition),
            array('date' => 'DESC')
        );
        return $this->render('ArmdPaperArchiveBundle:Default:paper-archive.html.twig', array(
            'category'    => $slug,
            'items'       => $items,
        ));
    }
}

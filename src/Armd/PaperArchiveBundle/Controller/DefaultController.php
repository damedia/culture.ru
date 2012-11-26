<?php

namespace Armd\PaperArchiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/press-centre/archive/", name="armd_paper_archive")
     * @Template()
     */
    public function indexAction($category=null)
    {
        $items = $this->getDoctrine()->getRepository('ArmdPaperArchiveBundle:PaperArchive')->findBy(
            array('category' => $category),
            array('date' => 'DESC')
        );
        return $this->render('ArmdPaperArchiveBundle:Default:paper-archive.html.twig', array(
            'category'  => 'archive',
            'items'   => $items
        ));
    }
}

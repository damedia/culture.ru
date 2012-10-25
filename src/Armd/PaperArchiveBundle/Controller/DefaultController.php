<?php

namespace Armd\PaperArchiveBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/press-centre/archive_new/")
     * @Template()
     */
    public function indexAction()
    {
        // Здесь надо получить данные
        $items = $this->getDoctrine()->getRepository('ArmdPaperArchiveBundle:PaperArchive')->findAll();

        // А тут надо выбрать шаблон и отдать его на отрисовку
        return $this->render('ArmdPaperArchiveBundle:Default:paper-archive.html.twig', array(
            'category'  => 'archive',
            'items'   => $items
        ));
    }
}

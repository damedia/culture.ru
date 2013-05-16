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

    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    /*public function getItemsSitemap($action = null, $params = array())
    {
        $items = array();

        switch ($action) {
            case 'indexAction': {
                isset($params['slug']) or $params['slug'] = null;

                $edition = $this->getDoctrine()->getRepository('ArmdPaperArchiveBundle:PaperEdition')->findOneBy(
                    array('slug' => $params['slug'])
                );

                $papers = $this->getDoctrine()->getRepository('ArmdPaperArchiveBundle:PaperArchive')->findBy(
                    array('edition' => $edition),
                    array('date' => 'DESC')
                );

                if ($papers) {
                    foreach ($papers as $p) {
                        $items[] = array(
                            'loc' => $this->container->get('sonata.media.twig.extension')->path($p->getFile(), 'reference'),
                            'lastmod' => null
                        );
                    }
                }

                break;
            }
        }

        return $items;
    }*/
}

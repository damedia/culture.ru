<?php
namespace Damedia\SpecialProjectBundle\Controller;

use \Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Response;

class PageCRUDController extends Controller {
    public function previewPageAction($id) {
        return new Response('This is an previewPageAction for page = '.$id);
    }

    public function editPageAction($id) {
        $page = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Page')->find($id);

        if (!$page) {
            throw $this->createNotFoundException('Page (id = "'.$id.'") not found!');
        }

        $template = $page->getTemplate();

        if (!$template) {
            throw $this->createNotFoundException('Page (id = "'.$id.'") has no Template!');
        }

        $templateSettings = $template->getTemplateSettings();
        $blocksForTwig = $page->renderBlocks($templateSettings);

        return $this->render('DamediaSpecialProjectBundle:Templates:'.$template->getTwigFileName(),
                             array('PageTitle' => $page->getTitle(),
                                   'Blocks' => $blocksForTwig));
    }
}
?>
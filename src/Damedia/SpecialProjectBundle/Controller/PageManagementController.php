<?php
namespace Damedia\SpecialProjectBundle\Controller;

use \Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PageManagementController extends Controller {
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

        $pageBlocks = $this->getBlocksForPageId($page->getId());
        $renderedBlocks = $this->renderPageBlocksToEdit($pageBlocks);

        return $this->render('DamediaSpecialProjectBundle:Templates:'.$template->getTwigFileName(),
                             array('PageTitle' => $page->getTitle(),
                                   'Blocks' => $renderedBlocks));
    }

    /**
     * @Route("/ajaxhandler", name="_admin_ajax_handler")
     */
    public function handlerAction() {

        $isAjax = $this->get('Request')->isXMLHttpRequest();
        if ($isAjax) {
            //...
            return new Response('This is ajax response');
        }
        return new Response('This is not ajax!', 400);
    }

    private function getBlocksForPageId($pageId) {
        return $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Block')->findBy(array('page' => $pageId));
    }

    private function renderPageBlocksToEdit(array $blocks) { //render blocks depending on settings
        $result = array();

        $chunks = $this->getChunksForBlocksArray($blocks);

        foreach ($blocks as $block) {
            $blockChunks = (isset($chunks[$block->getId()])) ? $chunks[$block->getId()] : array();
            $result[$block->getPlaceholder()] = $this->renderBlockContent($block->getPlaceholder(), $blockChunks);
        }

        return $result;
    }



    private function getChunksForBlocksArray(array $blocks) {
        $result = array();
        $blocksIds = array();

        foreach ($blocks as $block) {
            $blocksIds[] = $block->getId();
        }

        $chunks = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Chunk')->findBy(array('block' => $blocksIds));

        foreach ($chunks as $chunk) {
            $result[$chunk->getBlock()][] = $chunk->getContent();
        }

        return $result;
    }

    private function renderBlockContent($placeholder, array $blockChunks) {
        $result = '';

        if (count($blockChunks) == 0) {
            return '<textarea data-placeholder="'.$placeholder.'" class="editPage_blockContent"></textarea>';
        }

        foreach ($blockChunks as $chunk) {
            $result .= $chunk->getContent();
        }

        return $result;
    }
}
?>
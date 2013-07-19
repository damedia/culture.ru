<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;

class PageManagementController extends Controller {
    public function getTinyAcFormAction() {
        return $this->render('DamediaSpecialProjectBundle:Admin:pageAdmin_tinyAcForm.html.twig');
    }

    public function getTemplateBlocksFormAction() {
        $response = array('content' => '');

        $request = $this->get('request');
        $templateId = (integer)$request->request->get('templateId');
        $pageId = (integer)$request->request->get('pageId');

        if ($templateId >= 0) {
            $template = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Template')->find($templateId);
            $twigFileName = $template->getTwigFileName();

            $helper = $this->container->get('special_project_helper');
            $twigTemplatesPath = $helper->getTwigTemplatesPath($this->container->get('kernel'));
            $fileContent = file_get_contents($twigTemplatesPath.DIRECTORY_SEPARATOR.$twigFileName);

            $blocksPlaceholders = $helper->getBlockNamesFromString($fileContent);
            $blocks = $this->getBlocksForPageId($pageId);
            $chunks_mappedByPlaceholder = $this->getChunksForBlocks_mappedByPlaceholder($blocks);

            $formBuilder = $this->createFormBuilder();
            foreach ($blocksPlaceholders as $placeholder) {
                $blockContent = $this->getBlockContentByPlaceholder($placeholder, $chunks_mappedByPlaceholder);

                $formBuilder->add($placeholder, 'textarea',
                                  array('required' => false,
                                        'attr' => array('class' => 'createPage_blockTextarea'),
                                        'data' => $blockContent));
            }
            $form = $formBuilder->getForm();

            $response['content'] = $this->renderView('DamediaSpecialProjectBundle:Admin:pageAdmin_templateBlocksForm.html.twig',
                                                     array('twigFileName' => $twigFileName,
                                                           'form' => $form->createView()));
        }
        else {
            $response['content'] = 'Template ID has not been sent!';
        }

        return $this->renderJson($response);
    }



    private function getBlocksForPageId($pageId = null) {
        $result = array();

        if (!$pageId) {
            return $result;
        }

        return $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Block')->findByPage($pageId);
    }

    private function getChunksForBlocks_mappedByPlaceholder(array $blocks) {
        $result = array();
        $blocksMap = array();

        if (count($blocks) === 0) {
            return $result;
        }

        foreach ($blocks as $object) {
            $blocksMap[$object->getId()] = $object->getPlaceholder();
            $result[$object->getPlaceholder()] = array();
        }

        $chunks = $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Chunk')->findBy(array('block' => array_keys($blocksMap)));

        foreach ($chunks as $object) {
            $placeholder = $blocksMap[$object->getBlock()->getId()];
            $result[$placeholder][] = $object;
        }

        return $result;
    }

    private function getBlockContentByPlaceholder($placeholder, array $chunks) {
        $result = '';

        if (!isset($chunks[$placeholder])) {
            return $result;
        }

        foreach ($chunks[$placeholder] as $object) {
            $result .= $object->getContent();
        }

        return $result;
    }









    /*
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
    */

    /*

    */

    /*
    private function renderPageBlocksToEdit(array $blocks) { //render blocks depending on settings
        $result = array();

        $chunks = $this->getChunksForBlocksArray($blocks);

        foreach ($blocks as $block) {
            $blockChunks = (isset($chunks[$block->getId()])) ? $chunks[$block->getId()] : array();
            $result[$block->getPlaceholder()] = $this->renderBlockContent($block->getPlaceholder(), $blockChunks);
        }

        return $result;
    }
    */

    /*

    */

    /*
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
    */
}
?>
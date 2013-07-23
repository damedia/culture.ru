<?php
namespace Damedia\SpecialProjectBundle\Admin;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Damedia\SpecialProjectBundle\Entity\Page;
use Damedia\SpecialProjectBundle\Entity\Block;
use Damedia\SpecialProjectBundle\Entity\Chunk;

use Sonata\AdminBundle\Route\RouteCollection;

class PageAdmin extends Admin {
    const LABEL_ID = 'ID';
    const LABEL_TITLE = 'Название страницы';
    const LABEL_SLUG = 'Суффикс URL';
    const LABEL_CREATED = 'Дата создания';
    const LABEL_UPDATED = 'Дата изменения';
    const LABEL_IS_PUBLISHED = 'Опубликован';
    const LABEL_TEMPLATE_ID = 'Шаблон';

    const LABEL_ACTIONS = 'Управление';



    public function getTemplate($name) {
        switch ($name) {
            case 'edit': //and also 'create'
                return 'DamediaSpecialProjectBundle:Admin:pageAdmin_createOrEditPage.html.twig';
                break;

            default:
                return parent::getTemplate($name);
        }
    }



    public function postPersist($object) {
        $userData = $this->getBlocksContentFromRequest();
        $entityManager = $this->getEntityManager();

        foreach ($userData as $placeholder => $content) {
            $block = $this->createBlock($entityManager, $object, $placeholder);
            $this->createChunksForBlock($entityManager, $block, $content);
        }

        $entityManager->flush();
    }

    public function postUpdate($object) {
        $userData = $this->getBlocksContentFromRequest();
        $entityManager = $this->getEntityManager();

        $pageBlocks = $this->getBlocksForPageId($object->getId(), 'mappedByBlockPlaceholder'); // use $page->getBlocks() and do mapping!!!

        foreach ($userData as $placeholder => $content) {
            if (!isset($pageBlocks[$placeholder])) {
                $block = $this->createBlock($entityManager, $object, $placeholder);
            }
            else {
                $block = $pageBlocks[$placeholder];
                $this->removeAllChunksForBlock($entityManager, $block);
            }

            $this->createChunksForBlock($entityManager, $block, $content);
        }

        $entityManager->flush();
    }

    public function preRemove($object) {
        $pageBlocks = $this->getBlocksForPageId($object->getId(), 'mappedByBlockId'); // use $page->getBlocks() and do mapping!!!
        $blocksChunks = $this->getChunksForBlocks($pageBlocks);

        $entityManager = $this->getEntityManager();

        foreach ($blocksChunks as $chunk) {
            $entityManager->remove($chunk);
        }
        //$entityManager->flush();

        foreach ($pageBlocks as $block) {
            $entityManager->remove($block);
        }
        $entityManager->flush();
    }



    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('title', null,
            array('label' => $this::LABEL_TITLE));

        $formMapper->add('slug', null,
            array('label' => $this::LABEL_SLUG,
                  'required' => false));

        $formMapper->add('template', 'entity',
            array('label' => $this::LABEL_TEMPLATE_ID,
                  'class' => 'DamediaSpecialProjectBundle:Template',
                  'property' => 'title',
                  'empty_value' => '-- выберите шаблон --',
                  'attr' => array('class' => 'DamediaSpecialProjectBundle_templateSelect')));

        $formMapper->add('isPublished', null,
            array('label' => $this::LABEL_IS_PUBLISHED,
                  'required' => false));
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->add('id', null,
            array('label' => $this::LABEL_ID));

        $listMapper->addIdentifier('title', null,
            array('label' => $this::LABEL_TITLE));

        $listMapper->add('_action', 'actions',
            array('label' => $this::LABEL_ACTIONS,
                  'actions' => array('previewPage' => array('template' => 'DamediaSpecialProjectBundle:Admin:pageAdmin_previewPage.html.twig'),
                                     'editPage' => array('template' => 'DamediaSpecialProjectBundle:Admin:pageAdmin_editPage.html.twig'),
                                     'delete' => array('template' => 'DamediaSpecialProjectBundle:Admin:pageAdmin_deletePage.html.twig'))));

        $listMapper->add('slug', null,
            array('label' => $this::LABEL_SLUG));

        $listMapper->add('created', null,
            array('label' => $this::LABEL_CREATED));

        $listMapper->add('updated', null,
            array('label' => $this::LABEL_UPDATED));

        $listMapper->add('isPublished', null,
            array('label' => $this::LABEL_IS_PUBLISHED));

        $listMapper->add('template', null,
            array('label' => $this::LABEL_TEMPLATE_ID));
    }

    protected function configureRoutes(RouteCollection $collection) {
        $collection->add('previewPage', $this->getRouterIdParameter().'/previewpage');
        $collection->add('editPage', $this->getRouterIdParameter().'/editpage');
        $collection->add('delete', $this->getRouterIdParameter().'/delete');
    }



    private function getDoctrine() {
        $container = $this->getConfigurationPool()->getContainer();

        return $container->get('doctrine');
    }
    private function getBlockRepository() {
        return $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Block');
    }
    private function getChunkRepository() {
        return $this->getDoctrine()->getRepository('DamediaSpecialProjectBundle:Chunk');
    }
    private function getEntityManager() {
        return $this->getDoctrine()->getEntityManager();
    }




    private function getBlocksContentFromRequest() {
        $result = array();

        $sentData = $this->getRequest()->request;
        foreach ($sentData->get('form') as $placeholder => $blockContent) {
            if ($placeholder === '_token') {
                continue;
            }

            $result[$placeholder] = $blockContent;
        }

        return $result;
    }
    private function getBlocksForPageId($pageId, $mappedBy) { // remove this method!!!
        $result = array();

        $blockRepository = $this->getBlockRepository();
        $blocks = $blockRepository->findByPage($pageId);

        foreach ($blocks as $object) {
            switch ($mappedBy) {
                case 'mappedByBlockId':
                    $result[$object->getId()] = $object;
                    break;
                case 'mappedByBlockPlaceholder':
                    $result[$object->getPlaceholder()] = $object;
                    break;
                default:
                    $result[] = $object;
            }
        }

        return $result;
    }
    private function getChunksForBlocks(array $blocks) {
        $result = array();

        $chunkRepository = $this->getChunkRepository();
        $chunks = $chunkRepository->findBy(array('block' => array_keys($blocks)));

        foreach ($chunks as $object) {
            $blockId = $object->getBlock()->getId();
            $placeholder = $blocks[$blockId]->getPlaceholder();

            $result[$placeholder] = $object;
        }

        return $result;
    }
    private function extractChunksFromString($string) {
        $result = array();

        $result[] = $string; //parse string is omitted for now!

        return $result;
    }



    private function createBlock(EntityManager $entityManager, Page $page, $placeholder) {
        $block = new Block();
        $block->setPage($page);
        $block->setPlaceholder($placeholder);
        $entityManager->persist($block);

        return $block;
    }
    private function createChunksForBlock(EntityManager $entityManager, Block $block, $blockContentString) {
        $blockChunks = $this->extractChunksFromString($blockContentString);
        foreach ($blockChunks as $chunkContent) {
            $chunk = new Chunk();
            $chunk->setBlock($block);
            $chunk->setContent($chunkContent);
            $entityManager->persist($chunk);
        }
    }
    private function removeAllChunksForBlock(EntityManager $entityManager, Block $block) {
        $blockChunks = $this->getChunksForBlocks(array($block->getId() => $block));
        foreach ($blockChunks as $chunk) {
            $entityManager->remove($chunk);
        }
    }
}
?>
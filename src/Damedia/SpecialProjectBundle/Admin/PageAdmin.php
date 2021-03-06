<?php
namespace Damedia\SpecialProjectBundle\Admin;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Damedia\SpecialProjectBundle\Entity\Page;
use Damedia\SpecialProjectBundle\Entity\Block;
use Damedia\SpecialProjectBundle\Entity\Chunk;
use Damedia\SpecialProjectBundle\Form\Type\NewsSelectType;

use Sonata\AdminBundle\Route\RouteCollection;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Doctrine\ORM\EntityRepository;

class PageAdmin extends Admin {
    const LABEL_ID = 'ID';
    const LABEL_TITLE = 'Название страницы';
    const LABEL_SLUG = 'Суффикс URL';
    const LABEL_PARENT = 'Вложена в';
    const LABEL_CREATED = 'Дата создания';
    const LABEL_UPDATED = 'Дата изменения';
    const LABEL_STYLESHEET = 'CSS-стили';
    const LABEL_JAVASCRIPT = 'Javascript';
    const LABEL_IS_PUBLISHED = 'Опубликован';
    const LABEL_TEMPLATE_ID = 'Шаблон';
    const LABEL_SHOW_ON_MAIN = 'Показывать на главной';
    const LABEL_SHOW_ON_MAIN_FROM = 'Показывать на главной с';
    const LABEL_SHOW_ON_MAIN_TO = 'Показывать на главной до';
    const LABEL_BANNER_IMAGE = 'Баннер';
    const LABEL_NEWS = 'Связанные новости';

    const LABEL_ACTIONS = 'Управление';



    public function getTemplate($name) {
        switch ($name) {
            case 'edit': //and also 'create'
                return 'DamediaSpecialProjectBundle:Admin:pageAdmin_layout_createOrEditPage.html.twig';
                break;

            default:
                return parent::getTemplate($name);
        }
    }



    public function postPersist($object) {
        $userData = $this->getBlocksContentFromRequest();
        $entityManager = $this->getEntityManager();

        $container = $this->getConfigurationPool()->getContainer();
        $snippetParser = $container->get('special_project_snippet_parser');

        foreach ($userData as $placeholder => $content) {
            $snippetParser->html_to_entities($content);

            $block = $this->createBlock($entityManager, $object, $placeholder);
            $this->createChunksForBlock($entityManager, $block, $content);
        }

        $entityManager->flush();
    }

    public function postUpdate($object) {
        $userData = $this->getBlocksContentFromRequest();
        $entityManager = $this->getEntityManager();

        $container = $this->getConfigurationPool()->getContainer();
        $snippetParser = $container->get('special_project_snippet_parser');

        $pageBlocks = $this->getBlocksForPageId($object->getId(), 'mappedByBlockPlaceholder'); // use $page->getBlocks() and do mapping!!!

        foreach ($userData as $placeholder => $content) {
            $snippetParser->html_to_entities($content);

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

        foreach ($pageBlocks as $block) {
            $entityManager->remove($block);
        }
        $entityManager->flush();
    }



    protected function configureFormFields(FormMapper $formMapper) {
        $isNew = ($this->getSubject() && !$this->getSubject()->getId());
        $pageId = $isNew ? null : $this->getSubject()->getId();

        $formMapper->add('title', null,
            array('label' => $this::LABEL_TITLE));

        $formMapper->add('slug', null,
            array('label' => $this::LABEL_SLUG,
                  'required' => false));

        $formMapper->add('parent', null,
            array('label' => $this::LABEL_PARENT,
                  'required' => false));

        $formMapper->add('template', 'entity',
            array('label' => $this::LABEL_TEMPLATE_ID,
                  'class' => 'DamediaSpecialProjectBundle:Template',
                  'property' => 'title',
                  'empty_value' => '-- выберите шаблон --',
                  'attr' => array('class' => 'DamediaSpecialProjectBundle_templateSelect')));

        $formMapper->add('stylesheet', null,
            array('label' => $this::LABEL_STYLESHEET,
                  'required' => false));

        $formMapper->add('javascript', null,
            array('label' => $this::LABEL_JAVASCRIPT,
                  'required' => false));

        $formMapper->add('isPublished', null,
            array('label' => $this::LABEL_IS_PUBLISHED,
                  'required' => false));

        $formMapper->add('showOnMain', null,
            array('label' => $this::LABEL_SHOW_ON_MAIN,
                  'required' => false));

        $formMapper->add('showOnMainFrom', null,
            array('label' => $this::LABEL_SHOW_ON_MAIN_FROM,
                  'required' => false));

        $formMapper->add('showOnMainTo', null,
            array('label' => $this::LABEL_SHOW_ON_MAIN_TO,
                  'required' => false));

        $formMapper->add('bannerImage', 'armd_media_file_type',
            array('label' => $this::LABEL_BANNER_IMAGE,
                  'required' => $isNew,
                  'with_remove' => false,
                  'media_context' => 'sproject_banner',
                  'media_provider' => 'sonata.media.provider.image'));

        $formMapper->add('news', new NewsSelectType($pageId),
            array('label' => $this::LABEL_NEWS,
                  'required' => false));

        $formBuilder = $formMapper->getFormBuilder();
        $formFactory = $formBuilder->getFormFactory();
        $formBuilder->addEventListener(FormEvents::PRE_BIND, function(FormEvent $event) use ($formFactory, $pageId){
            $form = $event->getForm();

            if ($form->has('news')) {
                $form->remove('news');
            }

            $form->add($formFactory->createNamed('news', new NewsSelectType($pageId), null,
                array('required' => false,
                      'query_builder' => function(EntityRepository $er) {
                          return $er->createQueryBuilder('g');
                      })));
        });
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->add('id', null,
            array('label' => $this::LABEL_ID));

        $listMapper->addIdentifier('title', null,
            array('label' => $this::LABEL_TITLE));

        $listMapper->add('_action', 'actions',
            array('label' => $this::LABEL_ACTIONS,
                  'actions' => array('previewPage' => array('template' => 'DamediaSpecialProjectBundle:Admin:pageAdmin_listCell_previewPage.html.twig'))));

        $listMapper->add('slug', null,
            array('label' => $this::LABEL_SLUG));

        $listMapper->add('parent', null,
        	array('label' => $this::LABEL_PARENT));

        $listMapper->add('updated', null,
            array('label' => $this::LABEL_UPDATED));

        $listMapper->add('isPublished', null,
            array('label' => $this::LABEL_IS_PUBLISHED));

        $listMapper->add('showOnMain', null,
            array('label' => $this::LABEL_SHOW_ON_MAIN, 'editable' => true));

        $listMapper->add('template', null,
            array('label' => $this::LABEL_TEMPLATE_ID));

        $listMapper->add('news', null,
            array('label' => $this::LABEL_NEWS));
    }

    protected function configureRoutes(RouteCollection $collection) {
        $collection->add('previewPage', $this->getRouterIdParameter().'/previewpage');
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
        foreach ($sentData->get('form') as $placeholder => $blockContent) { // error if no twig template file found!!!
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
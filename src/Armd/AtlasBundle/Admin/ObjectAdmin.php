<?php

namespace Armd\AtlasBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;
use Armd\AtlasBundle\Entity\Category;
use Armd\AtlasBundle\Entity\TouristCluster;
use Sonata\AdminBundle\Route\RouteCollection;

class ObjectAdmin extends Admin
{
    protected $translationDomain = 'ArmdAtlasBundle';
    protected $container;

    public function __construct($code, $class, $baseControllerName, $serviceContainer)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $serviceContainer;
    }


    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('announce')
            ->add('content')
            ->add('siteUrl')
            ->add('email')
            ->add('phone')
            ->add('address')
            ->add('lat')
            ->add('lon')
            ->add('categories')
            ->add('workTime')
            ->add('weekends')
            ->add('images')
            ->add('videos')
            ->add('archiveImages')
            ->add('image3d')
            ->add('virtualTour')
            ->add('showOnMainAsRecommended', null, array('label' => 'Рекомендуемое на главной'))
            ->add('showOnMainAsNovel', null, array('label' => 'Неизведанное на главной'))
            ->add('touristCluster');
    }


    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('dcxId', 'hidden', array('required' => false))
                ->add('published', null, array('required' => false))
                ->add('corrected', null, array('required' => false,
                                               'disabled' => ($this->container->get('security.context')->isGranted('ROLE_CORRECTOR') ? false : true )))
                ->add('title')
                ->add('announce')
                ->add('content', null, array('attr' => array('class' => 'tinymce')))
                ->add('lat')
                ->add('lon')
//                ->add('categories', 'sonata_type_model', array('multiple' => true,
//                                                               'expanded' => true))
            ->end()

            ->with('Classification')
                ->add('primaryCategory', 'armd_atlas_object_categories', array('multiple' => false,
                                                                               'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
                                                                               'only_with_icon' => true,
                                                                               'empty_value' => '=== Не выбрано ==='))
                ->add('secondaryCategories', 'armd_atlas_object_categories', array('required' => true,
                                                                                   'attr' => array('class' => 'chzn-select atlas-object-categories-select')))
                ->add('tags', 'armd_tag', array('required' => false))
                ->add('touristCluster', null, array('required' => false,
                                                    'attr' => array('class' => 'chzn-select atlas-object-categories-select')))
                ->add('isOfficial', null, array('required' => false))
            ->end()

            ->with('Contacts')
                ->add('siteUrl')
                ->add('email')
                ->add('phone')
                ->add('regions', null, array('required' => false,
                                             'attr' => array('class' => 'chzn-select atlas-object-region-select')))
                ->add('address')
                ->add('workTime')
                ->add('weekends', null, array('multiple' => true,
                                              'expanded' => true,
                                              'required' => false,
                                              'attr' => array('class' => 'armd-sonata-weekdays')))
            ->end()

            ->with('Рекомендуемое на главной')
                ->add('showOnMainAsRecommended', null, array('required' => false,
                                                             'label' => 'Рекомендуемое на главной'))
                ->add('showOnMainAsRecommendedFrom', 'date', array('required' => false,
                                                                   'label' => 'С'))
                ->add('showOnMainAsRecommendedTo', 'date', array('required' => false,
                                                                 'label' => 'По'))
                ->add('showOnMainAsRecommendedOrd', null, array('required' => false,
                                                                'label' => 'Приоритет'))
            ->end()

            ->with('Неизведанное на главной')
                ->add('showOnMainAsNovel', null, array('required' => false,
                                                       'label' => 'Неизведанное на главной'))
                ->add('showOnMainAsNovelFrom', 'date', array('required' => false,
                                                             'label' => 'С'))
                ->add('showOnMainAsNovelTo', 'date', array('required' => false,
                                                           'label' => 'По'))
                ->add('showOnMainAsNovelOrd', null, array('required' => false,
                                                          'label' => 'Приоритет'))
            ->end()

            ->with('Moderation')
                ->add('status')
                ->add('reason')
            ->end()

            ->with('SEO')
                ->add('seoTitle', null, array('attr' => array('class' => 'span8')))
                ->add('seoKeywords')
                ->add('seoDescription')
            ->end()

            ->with('Russia Image')
                ->add('showAtRussianImage', null, array('required' => false))
                ->add('russiaImageAnnounce')
            ->end()

            ->with('Virtual Tour')
//                ->add('virtualTour')
//                ->add('virtualTourImage', 'armd_media_file_type', array('required' => false,
//                                                                        'with_remove' => true,
//                                                                        'media_context' => 'atlas',
//                                                                        'media_provider' => 'sonata.media.provider.image',
//                                                                        'media_format' => 'thumbnail'))
                ->add('virtualTours', null, array('required' => false,
                                                  'attr' => array('class' => 'chzn-select atlas-object-virtual-tours-select')))
            ->end()

            ->with('Media')
                ->add('primaryImage', 'armd_dcx_media_file_type', array(
                                                                    'with_remove' => false,
                                                                    'media_context' => 'atlas',
                                                                    'media_format' => 'thumbnail'))
                ->add('sideBannerImage', 'armd_media_file_type', array('required' => false,
                                                                       'with_remove' => true,
                                                                       'media_context' => 'atlas',
                                                                       'media_provider' => 'sonata.media.provider.image',
                                                                       'media_format' => 'thumbnail'))
                ->add('images', 'collection', array('type' => 'armd_dcx_media_file_type',
                                                    'options' => array('media_context' => 'atlas',
                                                                       'media_format' => 'thumbnail',
                                                                       'with_remove' => false),
                                                    'by_reference' => false,
                                                    'allow_add' => true,
                                                    'allow_delete' => true,
                                                    'required' => false,
                                                    'attr' => array('class' => 'armd-sonata-images-collection')))
                ->add('archiveImages', 'collection', array('type' => 'armd_media_file_type',
                                                           'options' => array('media_context' => 'atlas',
                                                                              'media_provider' => 'sonata.media.provider.image',
                                                                              'media_format' => 'thumbnail',
                                                                              'with_title' => true,
                                                                              'with_description' => true),
                                                           'by_reference' => false,
                                                           'allow_add' => true,
                                                           'allow_delete' => true,
                                                           'required' => false,
                                                           'attr' => array('class' => 'armd-sonata-images-collection')))
                ->add('image3d', 'armd_media_file_type', array('required' => false,
                                                               'with_remove' => true,
                                                               'media_context' => 'atlas',
                                                               'media_provider' => 'sonata.media.provider.image',
                                                               'media_format' => 'thumbnail'))
//                ->add('videos', 'collection', array('type' => 'armd_tvigle_video_selector',
//                                                    'by_reference' => false,
//                                                    'allow_add' => true,
//                                                    'allow_delete' => true,
//                                                    'attr' => array('class' => 'armd-sonata-tvigle-collection'),
//                                                    'options' => array('attr' => array('class' => 'armd-sonata-tvigle-form')),
//                                                    'label' => 'Видео (Tvigle ID)'))
                ->add('mediaVideos',  'collection', array('type' => 'armd_media_video_type',
                                                          'options' => array('media_context' => 'atlas',
                                                                             'media_provider' => 'sonata.media.provider.tvigle',
                                                                             'media_format' => 'thumbnail',
                                                                             'with_title' => true),
                                                          'by_reference' => false,
                                                          'allow_add' => true,
                                                          'allow_delete' => true,
                                                          'required' => false,
                                                          'attr' => array('class' => 'armd-sonata-images-collection'),
                                                          'label' => 'Видео (Tvigle ID)'))
            ->end()

            ->with('Literature')
                ->add('literatures', 'sonata_type_collection', array('by_reference' => false,
                                                                     'required' => true),
                                                               array('edit' => 'inline',
                                                                     'inline' => 'table'))
            ->end()

            ->with('Stuff')
            ->add('stuff', 'collection', array('type' => 'armd_media_file_type',
                                               'allow_add' => true,
                                               'allow_delete' => true,
                                               'options' => array('required' => false,
                                                                  'media_provider' => 'sonata.media.provider.file',
                                                                  'by_reference' => true,
                                                                  'media_context' => 'stuff',
                                                                  'media_format' => 'original',
                                                                  'with_remove' => false, // Удаление выше, на уровне коллекции.
                                                                  'with_title' => false,
                                                                  'with_description' => true),
                                               'attr' => array('class' => 'armd-sonata-images-collection')))
            ->end()

            ->with('Hints')
                ->add('objectHints', 'sonata_type_collection', array('by_reference' => false,
                                                                     'required' => true),
                                                               array('edit' => 'inline',
                                                                     'inline' => 'table'))
            ->end();

        parent::configureFormFields($formMapper);
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('published')
            ->add('corrected')
            ->add('showOnMainAsRecommended', null, array('label' => 'Рекомендуемое на главной', 'editable' => true))
            ->add('showOnMainAsNovel', null, array('label' => 'Неизведанное на главной', 'editable' => true))
            ->add('primaryCategory', null, array('template' => 'ArmdAtlasBundle:Admin:list_object_categories.html.twig'))
            ->add('secondaryCategories', null, array('template' => 'ArmdAtlasBundle:Admin:list_object_categories.html.twig'))
            ->add('touristCluster', null, array('template' => 'ArmdAtlasBundle:Admin:list_object_categories.html.twig'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title')
            ->add('category', 'doctrine_orm_callback', array(
                'callback' => array($this, 'getCategoriesFilter'),
                'field_type' => 'entity',
                'field_options' => array(
                    'class' => 'Armd\AtlasBundle\Entity\Category',
                    'query_builder' => function($er) {
                        $qb = $er->createQueryBuilder('c');
                        $qb->orderBy('c.root, c.lft', 'ASC');
                        return $qb;
                    }
                ),
            ))
            ->add('primaryCategory')
//            ->add('secondaryCategories')
            ->add('coordinatesAreEmpty', 'doctrine_orm_callback', array(
                'field_type' => 'checkbox',
                'callback' => array($this, 'getEmptyCoordinatesFilter')
            ))
            ->add('showAtRussianImage')
            ->add('showOnMainAsRecommended', null, array('label' => 'Рекомендуемое на главной'))
            ->add('showOnMainAsNovel', null, array('label' => 'Неизведанное на главной'))
            ->add('corrected');
    }

    public function getEmptyCoordinatesFilter($qb, $alias, $field, $value)
    {
        if(!empty($value['value'])) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->eq($alias.'.lon', 0),
                $qb->expr()->isNull($alias.'.lon'),
                $qb->expr()->eq($alias.'.lat', 0),
                $qb->expr()->isNull($alias.'.lat')
            ));
        }
    }

    public function getCategoriesFilter($qb, $alias, $field, $value)
    {
        if(!empty($value['value']) && $value['value'] instanceof Category) {
            $qb->leftJoin($alias.'.secondaryCategories', 'filter_sc')
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->eq($alias.'.primaryCategory', $value['value']->getId()),
                    $qb->expr()->eq('filter_sc.id', $value['value']->getId())
            ));
        }
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['publish'] = array(
            'label' => 'Публиковать'
        );
        $actions['unpublish'] = array(
            'label' => 'Снять публикацию'
        );

        $actions['ShowOnMainAsRecommended'] = array(
            'label'            => $this->trans('aShowOnMain', array(), 'SonataAdminBundle'),
            'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
        );
        $actions['NotShowOnMainAsRecommended'] = array(
            'label'            => $this->trans('aNotShowOnMain', array(), 'SonataAdminBundle'),
            'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
        );

        $actions['ShowOnMainAsNovel'] = array(
            'label'            => $this->trans('aShowOnMainAsNovel', array(), 'SonataAdminBundle'),
            'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
        );
        $actions['NotShowOnMainAsNovel'] = array(
            'label'            => $this->trans('aNotShowOnMainAsNovel', array(), 'SonataAdminBundle'),
            'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
        );

        return $actions;
    }

    public function getFormTheme() {
        $themes = parent::getFormTheme();
        $themes[] = 'ArmdAtlasBundle:Form:fields.html.twig';
        return $themes;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        if ($this->getSubject()->getShowAtRussianImage() && !$this->getSubject()->getSideBannerImage()) {
            $errorElement->addViolation('Для Образа России должен быть задан Боковой баннер');
//            $errorElement
//                ->with('sideBannerImage')
//                    ->assertNotNull()
//                    ->addViolation('Для Образа России должен быть задан Боковой баннер')
//                ->end();
        }
    }

    public function postPersist($object)
    {
        parent::postPersist($object);
        $this->saveTagging($object);
    }

    public function postUpdate($object)
    {
        parent::postUpdate($object);
        $this->saveTagging($object);
    }

    protected function saveTagging($object)
    {
        $this->container->get('fpn_tag.tag_manager')->saveTagging($object);
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'list':
                return 'ArmdAtlasBundle:CRUD:list.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('checkArticle', null, array(), array('_method'=>'POST'));
        $collection->add('craeteArticle','createArticle/{dcxId}',array(), array(), array('expose' => true));
    }

}

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
            ->add('showOnMain')
            ->add('showOnMainOrd')
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
                ->add('published', null, array('required' => false))
                ->add('title')
                ->add('announce')
                ->add('content', null, array(
                    'attr' => array('class' => 'tinymce'),
                ))
//                ->add('categories', 'sonata_type_model',
//                    array('multiple' => true, 'expanded' => true)
//                )
            ->with('Главная')
                ->add('showOnMain', null, array(
                    'required' => false
                ))
                ->add('showOnMainOrd', null, array(
                    'required' => false
                ))
            ->with('Classification')
                ->add('primaryCategory', 'armd_atlas_object_categories',
                array(
                    'multiple' => false,
                    'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
                    'only_with_icon' => true,
                    'empty_value' => '=== Не выбрано ==='
                ))
                ->add('secondaryCategories', 'armd_atlas_object_categories', array(
                    'required' => true,
                    'attr' => array('class' => 'chzn-select atlas-object-categories-select')
                ))
                ->add('tags', 'armd_tag', array(
                    'required' => false,
                ))
                ->add('touristCluster', null, array(
                    'required' => false,
                    'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
                ))
                ->add('isOfficial', null, array('required' => false))
            ->end()
            ->with('Moderation')
                ->add('status')
                ->add('reason')
            ->end()
            ->with('SEO')
                ->add('seoTitle')
                ->add('seoKeywords')
                ->add('seoDescription')
            ->end()
            ->with('Russia Image')
                ->add('showAtRussianImage', null,
                    array('required' => false)
                )
                ->add('russiaImageAnnounce')
            ->end()
            ->with('Virtual Tour')
//                ->add('virtualTour')
//                ->add('virtualTourImage', 'armd_media_file_type', array(
//                    'required' => false,
//                    'with_remove' => true,
//                    'media_context' => 'atlas',
//                    'media_provider' => 'sonata.media.provider.image',
//                    'media_format' => 'thumbnail'
//                ))
                ->add(
                    'virtualTours',
                    null,
                    array(
                        'required' => false,
                        'attr' => array('class' => 'chzn-select atlas-object-virtual-tours-select')
                    )
                )
            ->end()
            ->with('Contacts')
                ->add('siteUrl')
                ->add('email')
                ->add('phone')
                ->add('regions', null, array(
                    'required' => false,
                    'attr' => array('class' => 'chzn-select atlas-object-region-select')
                ))
                ->add('address')
                ->add('lat')
                ->add('lon')
                ->add('workTime')
                ->add('weekends', null,
                    array(
                        'multiple' => true,
                        'expanded' => true,
                        'required' => false,
                        'attr' => array('class' => 'armd-sonata-weekdays')
                    )
                )
            ->end()
            ->with('Media')
                ->add('primaryImage', 'armd_media_file_type', array(
                    'required' => false,
                    'with_remove' => true,
                    'media_context' => 'atlas',
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'thumbnail'
                ))
                ->add('sideBannerImage', 'armd_media_file_type', array(
                    'required' => false,
                    'with_remove' => true,
                    'media_context' => 'atlas',
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'thumbnail'
                ))
                ->add('images', 'collection', array(
                    'type' => 'armd_media_file_type',
                    'options' => array(
                        'media_context' => 'atlas',
                        'media_provider' => 'sonata.media.provider.image',
                        'media_format' => 'thumbnail',
                        'with_title' => true,
                    ),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => false,
                    'attr' => array('class' => 'armd-sonata-images-collection'),
                ))
                ->add('archiveImages', 'collection', array(
                    'type' => 'armd_media_file_type',
                    'options' => array(
                        'media_context' => 'atlas',
                        'media_provider' => 'sonata.media.provider.image',
                        'media_format' => 'thumbnail',
                        'with_title' => true,
                        'with_description' => true
                    ),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => false,
                    'attr' => array('class' => 'armd-sonata-images-collection'),
                ))
                ->add('image3d', 'armd_media_file_type', array(
                    'required' => false,
                    'with_remove' => true,
                    'media_context' => 'atlas',
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'thumbnail'
                ))
                ->add('videos', 'collection', array(
                    'type' => 'armd_tvigle_video_selector',
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'attr' => array('class' => 'armd-sonata-tvigle-collection'),
                    'options' => array('attr' => array('class' => 'armd-sonata-tvigle-form')),
                    'label' => 'Видео (Tvigle ID)'
                ))
            ->end()
            ->with('Literature')
                ->add('literatures', 'sonata_type_collection',
                    array(
                        'by_reference' => false,
                        'required' => true,
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table'
                    )
                )
            ->end()
            ->with('Stuff')
            ->add(
                'stuff',
                'collection',
                array(
                    'type' => 'armd_media_file_type',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'options' => array(
                        'required' => false,
                        'media_provider' => 'sonata.media.provider.file',
                        'by_reference' => true,
                        'media_context' => 'stuff',
                        'media_format' => 'original',
                        'with_remove' => false, // Удаление выше, на уровне коллекции.
                        'with_title' => false,
                        'with_description' => true,
                    ),
                    'attr' => array('class' => 'armd-sonata-images-collection'),
                )
            )
            ->end()
            ->with('Hints')
                ->add('objectHints', 'sonata_type_collection',
                    array(
                        'by_reference' => false,
                        'required' => true,
                    ),
                    array(
                        'edit' => 'inline',
                        'inline' => 'table'
                    )
                )
            ->end()
        ;
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
            ->add('showOnMain')
            ->add('showOnMainOrd')
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
//            ->add('primaryCategory')
//            ->add('secondaryCategories')
            ->add('coordinatesAreEmpty', 'doctrine_orm_callback', array(
                'field_type' => 'checkbox',
                'callback' => array($this, 'getEmptyCoordinatesFilter')
            ))
            ->add('showAtRussianImage')
            ->add('showOnMain')
            ->add('showOnMainOrd');                        
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

        $actions['ShowOnMain']=array(
            'label'            => $this->trans('aShowOnMain', array(), 'SonataAdminBundle'),
            'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
        );
        $actions['NotShowOnMain']=array(
            'label'            => $this->trans('aNotShowOnMain', array(), 'SonataAdminBundle'),
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

}

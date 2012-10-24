<?php

namespace Armd\AtlasBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;
use Armd\AtlasBundle\Entity\Category;

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
            ->add('showAtHomepage');
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
                ->add('content')
//                ->add('categories', 'sonata_type_model',
//                    array('multiple' => true, 'expanded' => true)
//                )
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
                ->add('showAtHomepage', null,
                    array('required' => false)
                )
                ->add('isOfficial', null, array('required' => false))
                ->add('status')
                ->add('reason')
            ->end()
            ->with('Russia Image')
                ->add('showAtRussianImage', null,
                    array('required' => false)
                )
                ->add('russiaImageAnnounce')
            ->end()
            ->with('Virtual Tour')
                ->add('virtualTour')
                ->add('virtualTourImage', 'armd_media_file_type', array(
                    'required' => false,
                    'with_remove' => true,
                    'media_context' => 'atlas',
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'thumbnail'
                ))
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
                  ->add('images', 'collection', array(
                        'type' => 'armd_media_file_type',
                        'options' => array(
                            'media_context' => 'atlas',
                            'media_provider' => 'sonata.media.provider.image',
                            'media_format' => 'thumbnail'
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
//        echo get_class($this->container->get('armd_tvigle.admin.tvigle'));
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
            ->add('primaryCategory', null, array('template' => 'ArmdAtlasBundle:Admin:list_object_categories.html.twig'))
            ->add('secondaryCategories', null, array('template' => 'ArmdAtlasBundle:Admin:list_object_categories.html.twig'));
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
            ));
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

        return $actions;
    }

    public function getFormTheme() {
        $themes = parent::getFormTheme();
        $themes[] = 'ArmdAtlasBundle:Form:fields.html.twig';
        $themes[] = 'ArmdMediaHelperBundle:Form:fields.html.twig';
        return $themes;
    }

}

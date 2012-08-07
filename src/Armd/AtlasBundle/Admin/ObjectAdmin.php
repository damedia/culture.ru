<?php

namespace Armd\AtlasBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

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
                ->add('title')
                ->add('announce')
                ->add('content')
                ->add('categories', 'sonata_type_model',
                    array('multiple' => true, 'expanded' => true)
                )
                ->add('showAtHomepage', null,
                    array('required' => false)
                )
                ->add('siteUrl')
                ->add('email')
                ->add('phone')
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
                ->add('virtualTour')
            ->end()
            ->with('Media')
                  ->add('images', 'collection', array(
                        'type' => 'armd_media_image',
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'required' => false,
                        'attr' => array('class' => 'armd-sonata-images-collection')
                  ))
                  ->add('archiveImages', 'collection', array(
                        'type' => 'armd_media_image',
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'required' => false,
                        'attr' => array('class' => 'armd-sonata-images-collection')
                  ))
                ->add('image3d', 'armd_media_image', array(
                    'required' => false,
                    'with_remove' => true
                ))
                ->add('videos', 'collection', array(
                    'type' => $this->container->get('armd_tvigle.type.tvigle'),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'attr' => array('class' => 'armd-sonata-tvigle-collection'),
                    'options' => array('attr' => array('class' => 'armd-sonata-tvigle-form'),
                    )
                ))
//                ->add('videos', 'sonata_type_model',
//                    array(
//                        'required' => false,
//                        'multiple' => true,
//                        'expanded' => true,
//                        'attr' => array('style' => 'height: 250px')
//                    ))
//                ->add('videos', 'sonata_type_admin')
//                ->add('videos', 'collection', array(
//                    'type' => 'sonata_type_admin',
//                    'by_reference' => false,
//                    'allow_add' => true,
//                    'allow_delete' => true,
//                    'required' => false,
//                    'options' => array('sonata_field_description' => array(
//                        'admin' => 'armd_tvigle.admin.tvigle',
//                    ))

//                ))
            ->end();
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
            ->add('categories', null, array('template' => 'ArmdAtlasBundle:Admin:list_object_categories.html.twig'));
    }


    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title')
            ->add('categories');
    }

}

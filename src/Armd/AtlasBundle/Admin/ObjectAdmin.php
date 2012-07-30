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
            ->add('categories', 'sonata_type_model', array(
            'multiple' => true,
            'expanded' => true,))
            ->add('showAtHomepage', null, array('required' => false))
            ->add('title')
            ->add('announce')
            ->add('content')
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
            ))
            ->add('virtualTour')
            ->end()
            ->with('Media')
            ->add('imageGallery', 'sonata_type_model_list', array('required' => false),
            array(
                'link_parameters' => array('context' => 'atlas'),
            ))
            ->add('archiveImageGallery', 'sonata_type_model_list', array('required' => false),
            array(
                'link_parameters' => array('context' => 'atlas'),
            ))
//            ->add('images', 'sonata_type_model_list',
//            array('required' => false),
//            array(
//                'link_parameters' => array(
//                    'context' => 'atlas',
//                    'provider' => 'sonata.media.provider.image'
//                )
//            ))
//            ->add('videos')
//            ->add('archiveImages', 'sonata_type_model', array(), array('link_parameters'=>array('context'=>'atlas')))
//            ->add('image3d', 'sonata_type_model', array(), array('link_parameters'=>array('context'=>'atlas')))

//        array(
//            'link_parameters'=>array('context'=>'atlas'),
//        ))

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
            ->add('categories', null, array('template' => 'ArmdAtlasBundle:Admin:list_object_categories.html.twig'));
    }


    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('title')
            ->add('categories');
    }

}

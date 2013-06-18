<?php

namespace Armd\MuseumBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;


class WarGalleryMemberAdmin extends Admin
{
    protected $translationDomain = 'ArmdMuseumBundle';

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('published')
            ->add('name')
            ->add('years')
            ->add('ranks')
            ->add('description')
        ;
        
        parent::configureShowField($showMapper);        
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
                ->add('published')
                ->add('name')
                ->add('years')
                ->add('ranks')
                ->add('description', null, array(
                    'attr' => array('class' => 'tinymce'),
                ))
            ->end()
            ->with('Media')
                ->add('preview', 'sonata_type_model_list', array('required' => false), array('link_parameters' => array('context' => 'war_gallery')))
                ->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters' => array('context' => 'war_gallery')))
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
            ->addIdentifier('name')
            ->add('years')
            ->add('ranks')
            ->add('published')
        ;
        
        parent::configureListFields($listMapper);        
    }    
    
    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('published')
            ->add('name')
        ;
    }    
}

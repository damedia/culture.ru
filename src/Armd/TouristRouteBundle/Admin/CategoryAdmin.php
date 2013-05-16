<?php

namespace Armd\TouristRouteBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class CategoryAdmin extends Admin
{
    protected $translationDomain = 'ArmdTouristRouteBundle';
    
    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('slug');
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
                ->add('icon', 'armd_media_file_type', array(
                    'media_provider' => 'sonata.media.provider.image',
                    'media_context' => 'route_category',
                    'media_format' => 'default',
                    'with_remove' => true,
                    'required' => false
                ))
            ->end();
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
            ->add('icon', null, array(
                'template' => 'ArmdAtlasBundle:Admin:list_category_icon.html.twig'
            ))
        ;
    }
}

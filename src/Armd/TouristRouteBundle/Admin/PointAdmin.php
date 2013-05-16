<?php

namespace Armd\TouristRouteBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class PointAdmin extends Admin
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
            ->add('lat')
            ->add('lon')
            ->add('show');
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
                ->add('show', null, array('required' => false))
            ->end()
            ->with('Coordinates')
                ->add('lat', null, array('required' => true))
                ->add('lon', null, array('required' => true))
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
            ->add('lat')
            ->add('lon')
            ->add('show')
        ;
    }
}

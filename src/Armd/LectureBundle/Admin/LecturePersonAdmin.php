<?php

namespace Armd\LectureBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class LecturePersonAdmin extends Admin
{
    protected $translationDomain = 'ArmdLectureBundle';

    /**
     * List view
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'delete' => array(),
                    'view' => array(),
                    'edit' => array(),
                )
            ))
        ;
    }

    /**
     * Show view
     *
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
        ;
    }

    /**
     * Edit view
     *
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
        ;
    }

}
<?php

namespace Armd\AtlasBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class LiteratureAdmin extends Admin
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
        $showMapper->add('title');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('title', null, array('required' => true));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('title');
    }


}
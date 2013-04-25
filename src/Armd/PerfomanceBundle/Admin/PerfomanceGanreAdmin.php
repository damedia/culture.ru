<?php

namespace Armd\PerfomanceBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Armd\AtlasBundle\Util\TreeRepairer;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class PerfomanceGanreAdmin extends Admin
{

    protected $translationDomain = 'ArmdPerfomanceBundle';
    protected $baseRouteName = 'perfomance_ganre';
    protected $baseRoutePattern = 'perfomance_ganre';


    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title');
    }


    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
           ->add('title');
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title');
    }

    public function getBatchActions()
    {
        return array();
    }

}
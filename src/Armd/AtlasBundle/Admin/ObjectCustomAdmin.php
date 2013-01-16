<?php

namespace Armd\AtlasBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;
use Armd\AtlasBundle\Entity\Category;

class ObjectCustomAdmin extends Admin
{
    protected $translationDomain = 'ArmdAtlasBundle';
    protected $container;
    protected $baseRoutePattern = '/armd/atlas/objectcustom';
    protected $baseRouteName = 'admin_armd_atlas_objectcustom';

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
            ->add('address')
            ->add('lat')
            ->add('lon')
            ->add('categories')
            ->add('images');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
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
            ->add('primaryCategory', null, array('template' => 'ArmdAtlasBundle:Admin:list_object_categories.html.twig'))
            ->add('secondaryCategories', null, array('template' => 'ArmdAtlasBundle:Admin:list_object_categories.html.twig'))
            ->add('published')
            ->add('isOfficial')
            ->add('status')
            ->add('createdBy')
            ->add('createdAt')
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'list':
                return 'ArmdAtlasBundle:Admin:objectcustom_list.html.twig';
                break;
            case 'edit':
                return 'ArmdAtlasBundle:Admin:objectcustom_edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

}

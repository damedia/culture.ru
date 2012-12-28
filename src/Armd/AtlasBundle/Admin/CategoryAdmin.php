<?php

namespace Armd\AtlasBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Armd\AtlasBundle\Util\TreeRepairer;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class CategoryAdmin extends Admin
{
    protected $maxPerPage = 2500;
    protected $maxPageLinks = 2500;
    protected $translationDomain = 'ArmdAtlasBundle';
    protected $container;

    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'lft'
    );

    public function __construct($code, $class, $baseControllerName, $container)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;
    }

    public function createQuery($context = 'list')
    {
        $queryBuilder = $this->modelManager->getEntityManager('Armd\AtlasBundle\Entity\Category')
            ->createQueryBuilder('c')
            ->select('c')
            ->from('ArmdAtlasBundle:Category', 'c')
            ->where('c.parent IS NOT NULL');

        $query = new ProxyQuery($queryBuilder);
        return $query;
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
            ->add('description')
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
            ->add('parent', null,
            array(
                'required' => true,
                'property' => 'dotLeveledTitle',
                'label' => 'Parent category',
                'query_builder' => function($er)
                {
                    $qb = $er->createQueryBuilder('c');
                    $qb->orderBy('c.root, c.lft', 'ASC');
                    return $qb;
                }
            ))
            ->add('title')
            ->add('description')
            ->add('iconMedia', 'armd_media_file_type',
            array(
                'media_provider' => 'sonata.media.provider.image',
                'media_context' => 'atlas_icon',
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
            ->add('up', 'text', array('template' => 'ArmdAtlasBundle:Admin:list_category_up.html.twig', 'label'=>' '))
            ->add('down', 'text', array('template' => 'ArmdAtlasBundle:Admin:list_category_down.html.twig', 'label'=>' '))
            ->addIdentifier('spaceLeveledTitle', null,
            array(
                'template' => 'ArmdAtlasBundle:Admin:list_raw.html.twig',
                'label' => 'Title'
            ))
            ->add('iconMedia', null,
            array(
                'template' => 'ArmdAtlasBundle:Admin:list_category_icon.html.twig'
            ))
//            ->add('actions', 'text', array('template' => 'ArmdAtlasBundle:Admin:list_category_row_actions.html.twig'))
        ;
    }

    public function getFormTheme()
    {
        $themes = parent::getFormTheme();
        $themes[] = 'ArmdMediaHelperBundle:Form:fields.html.twig';
        return $themes;
    }

    public function getBatchActions()
    {
        return array();
    }


}
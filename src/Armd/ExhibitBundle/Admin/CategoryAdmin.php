<?php

namespace Armd\ExhibitBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\Admin;

class CategoryAdmin extends Admin
{
    protected $maxPerPage = 2500;
    protected $maxPageLinks = 2500;
    protected $translationDomain = 'ArmdExhibitBundle';
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
        $queryBuilder = $this->modelManager->getEntityManager('Armd\ExhibitBundle\Entity\Category')
            ->createQueryBuilder('c')
            ->select('c')
            ->from('ArmdExhibitBundle:Category', 'c')
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
            ->add('published')
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
            ->add('published', null, array('required' => false))
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
            ->add('up', 'text', array('template' => 'ArmdExhibitBundle:Admin:list_category_up.html.twig', 'label'=>' '))
            ->add('down', 'text', array('template' => 'ArmdExhibitBundle:Admin:list_category_down.html.twig', 'label'=>' '))
            ->addIdentifier('spaceLeveledTitle', null,
            array(
                'template' => 'ArmdExhibitBundle:Admin:list_raw.html.twig',
                'label' => 'Title'
            ))
            ->add('published')
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
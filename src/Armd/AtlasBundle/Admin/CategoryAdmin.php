<?php

namespace Armd\AtlasBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
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

    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'lft'
    );

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
            ->add('icon');
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
            ->add('icon')
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
            ->addIdentifier('spaceLeveledTitle', null,
            array(
                'template' => 'ArmdAtlasBundle:Admin:list_raw.html.twig',
                'label' => 'Title'
            ))
            ->add('icon', null,
            array(
                'template' => 'ArmdAtlasBundle:Admin:list_category_icon.html.twig',
                'sortable' => false
            ));
    }

//    public function preRemove($object)
//    {
//        $em = $this->modelManager->getEntityManager($object);
//        $repo = $em->getRepository("ArmdAtlasBundle:Category");
//        $subtree = $repo->childrenHierarchy($object);
//
//        foreach ($subtree AS $el){
//            $menus = $em->getRepository('ArmdAtlasBundle:Object')
//                        ->findBy(array('page'=> $el['id']));
//
//            foreach ($menus AS $m){
//                $em->remove($m);
//            }
//
//            $services = $em->getRepository('ShtumiPravBundle:Service')
//                           ->findBy(array('page'=> $el['id']));
//
//            foreach ($services AS $s){
//                $em->remove($s);
//            }
//            $em->flush();
//        }
//    }

}
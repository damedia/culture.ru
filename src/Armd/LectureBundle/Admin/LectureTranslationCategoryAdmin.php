<?php

namespace Armd\LectureBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Armd\AtlasBundle\Util\TreeRepairer;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class LectureTranslationCategoryAdmin extends Admin
{
    protected $maxPerPage = 2500;
    protected $maxPageLinks = 2500;
    protected $translationDomain = 'ArmdLectureBundle';
    protected $classnameLabel = 'TranslationCategory';
    protected $baseRouteName = 'lecture_translation_category';
    protected $baseRoutePattern = 'lecture_translation_category';


    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'lft'
    );

    public function createQuery($context = 'list')
    {
        $rootCategory = $this->modelManager->getEntityManager('Armd\LectureBundle\Entity\LectureCategory')
            ->createQueryBuilder()
            ->select('c')
            ->from('ArmdLectureBundle:LectureCategory', 'c')
            ->innerJoin('c.lectureSuperType', 'st')
            ->where('st.code = :superTypeCode')
            ->setParameters(array(
                'superTypeCode' => 'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION'
            ))->getQuery()->getSingleResult();

        $queryBuilder = $this->modelManager->getEntityManager('Armd\LectureBundle\Entity\LectureCategory')
            ->createQueryBuilder()
            ->select('c')
            ->from('ArmdLectureBundle:LectureCategory', 'c')
            ->where('c.parent IS NOT NULL')
            ->andWhere('c.root = :root_category_id')
            ->setParameters(array(
                'root_category_id' => $rootCategory->getId()
            ));

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
                'template' => 'ArmdLectureBundle:Admin:list_raw.html.twig',
                'label' => 'Title'
            ));
    }

//    public function postPersist($object)
//    {
//        $this->checkAndRepairTree();
//        parent::postPersist($object);
//    }
//
//    public function postUpdate($object)
//    {
//        $this->checkAndRepairTree();
//        parent::postUpdate($object);
//
//    }
//
//    public function checkAndRepairTree()
//    {
//        $em = $this->container->get('doctrine')->getManager();
//        $repo = $em->getRepository("ArmdAtlasBundle:Category");
//
//        $errors = $repo->verify();
//        if(is_array($errors)) {
//            $treeRepairer = new TreeRepairer();
//            $treeRepairer->rebuildTree($em, $repo);
//        }
//    }

}
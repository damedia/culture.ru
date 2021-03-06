<?php

namespace Armd\LectureBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityRepository;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;
use Armd\LectureBundle\Entity\Lecture;

class LectureNewsAdmin extends Admin
{
    protected $translationDomain = 'ArmdLectureBundle';
    protected $classnameLabel = 'Videos';
    protected $baseRouteName = 'lecture_news';
    protected $baseRoutePattern = 'lecture_news';


    public function createQuery($context = 'list')
    {

        $qb = $this->modelManager->getEntityManager('ArmdLectureBundle:Lecture')
            ->getRepository('ArmdLectureBundle:Lecture')->createQueryBuilder('l')
            ->innerJoin('l.lectureSuperType', 'st')
            ->where('st.code = \'LECTURE_SUPER_TYPE_NEWS\'');

        return new ProxyQuery($qb);
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
            ->add('corrected')
            ->add('title')
            ->add('description')
            ->add('categories')
            ->add('createdAt')
            ->add('lecturer')
            ->add('lectureVideo')
            ->add('lectureFile')
//            ->add('showOnMainAsRecommended')
//            ->add('showOnMainAsRecommendedOrd')
            ->add('isHeadline')
            ;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $lecture = $this->getSubject();
        $superType = $this->modelManager->getEntityManager('ArmdLectureBundle:LectureSuperType')
            ->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode('LECTURE_SUPER_TYPE_NEWS');


        $lecture->setLectureSuperType($superType);


        $formMapper
            ->add('published')
            ->add('corrected', null, array('required' => false, 'disabled' => ($this->container->get('security.context')->isGranted('ROLE_CORRECTOR') ? false : true )))
            ->add('title')
            ->add('announce')
            ->add('description', null, array(
                'attr' => array('class' => 'tinymce'),
            ))
//            ->add('categories', 'armd_lecture_categories', array(
//                'required' => false,
//                'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
//                'super_type' => $superType
//            ))
/*            ->add('genres1', 'entity',
                array(
                    'class' => 'ArmdLectureBundle:LectureGenre',
                    'multiple' => 'true',
                    'query_builder' => function (EntityRepository $er) use ($superType) {
                        return $er->createQueryBuilder('g')
                            ->where('g.level = 1')
                            ->andWhere('g.lectureSuperType = :lecture_super_type')
                            ->setParameter('lecture_super_type', $superType);
                    },
                    'attr' => array('class' => 'chzn-select')
                )
            )
            ->add('genres2', 'entity',
                array(
                    'class' => 'ArmdLectureBundle:LectureGenre',
                    'multiple' => 'true',
                    'query_builder' => function (EntityRepository $er) use ($superType) {
                        return $er->createQueryBuilder('g')
                            ->where('g.level = 2')
                            ->andWhere('g.lectureSuperType = :lecture_super_type')
                            ->setParameter('lecture_super_type', $superType);
                    },
                    'attr' => array('class' => 'chzn-select')
                )
            )*/
            ->add('tags', 'armd_tag', array('required' => false, 'attr' => array('class' => 'select2-tags')))
            ->add('isHeadline', null, array('required' => false))
//            ->with('Главная')
//                ->add('showOnMainAsRecommended', null, array(
//                    'required' => false
//                ))
//                ->add('showOnMainAsRecommendedOrd', null, array(
//                    'required' => false
//                ))
//            ->end()
            /*->with('Tvigle Video')
                ->add('lectureVideo', 'armd_tvigle_video_selector', array( 'required' => false))
            ->end()*/
            ->with('Video')
                ->add('mediaLectureVideo', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lecture')))
            ->end()
            ->with('External Video')
                ->add('externalUrl', null, array('required' => false))
            ->end()
        ;
    }


    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('published')
            ->add('corrected')
            ->add('title')
            ->add('categories')
//            ->add('showOnMain')
//            ->add('showOnMainOrd')
            ->add('isHeadline');
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
            ->add('published')
            ->add('corrected')
//            ->add('showOnMain')
//            ->add('showOnMainOrd')
            ->add('createdAt')
//            ->add('genres', null, array('template' => 'ArmdLectureBundle:Admin:list_lecture_categories.html.twig'))
        ;
    }

    public function getFormTheme()
    {
        $themes = parent::getFormTheme();
        $themes[] = 'ArmdLectureBundle:Form:fields.html.twig';
        $themes[] = 'ArmdMediaHelperBundle:Form:fields.html.twig';
        return $themes;
    }

    public function getBatchActions()
    {
        // retrieve the default (currently only the delete action) actions
        $actions = parent::getBatchActions();


        // check user permissions
        if($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')){
            // /*
            $actions['ShowOnMain']=array(
                'label'            => $this->trans('aShowOnMain', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
            );
            $actions['NotShowOnMain']=array(
                'label'            => $this->trans('aNotShowOnMain', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
            );
            // */
        }

        return $actions;
    }

    public function postPersist($object)
    {
        parent::postPersist($object);
        $this->saveTagging($object);
        $this->clearHeadline($object);
    }

    public function postUpdate($object)
    {
        parent::postUpdate($object);
        $this->saveTagging($object);
        $this->clearHeadline($object);
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    protected function saveTagging($object)
    {
        $this->container->get('fpn_tag.tag_manager')->saveTagging($object);
    }

    /**
     * Updates all LECTURE_SUPER_TYPE_NEWS to set $object the only headline
     */
    protected function clearHeadline($object)
    {
        $this->modelManager
                ->getEntityManager('ArmdLectureBundle:Lecture')
                ->createQuery('UPDATE ArmdLectureBundle:Lecture l
                                SET l.isHeadline = false
                                WHERE l.lectureSuperType = :lst AND l.id <> :oid AND l.isHeadline = true')
                ->execute(array(
                    ':lst' => $object->getLectureSuperType()->getId(),
                    ':oid' => $object->getId(),
                ));
    }
}
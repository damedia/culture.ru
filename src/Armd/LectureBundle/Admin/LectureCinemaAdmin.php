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

class LectureCinemaAdmin extends Admin
{
    protected $translationDomain = 'ArmdLectureBundle';
    protected $classnameLabel = 'Cinema';
    protected $baseRouteName = 'lecture_cinema';
    protected $baseRoutePattern = 'lecture_cinema';


    public function createQuery($context = 'list')
    {

        $qb = $this->modelManager->getEntityManager('ArmdLectureBundle:Lecture')
            ->getRepository('ArmdLectureBundle:Lecture')->createQueryBuilder('l')
            ->innerJoin('l.lectureSuperType', 'st')
            ->where('st.code = \'LECTURE_SUPER_TYPE_CINEMA\'');

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
            ->add('title')
            ->add('description')
            ->add('createdAt')
            ->add('lecturer')
            ->add('recommended')
            ->add('showAtSlider')
            ->add('showAtFeatured')
            ->add('isTop100Film')
            ->add('lectureVideo')
            ->add('lectureFile')
            ->add('showOnMain')
            ->add('showOnMainOrd')
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
            ->findOneByCode('LECTURE_SUPER_TYPE_CINEMA');


        $lecture->setLectureSuperType($superType);


        $formMapper
            ->add('published')
            ->add('title')
            ->add('description', null, array(
                'attr' => array('class' => 'tinymce'),
            ))
//            ->add('categories', 'armd_lecture_categories', array(
//                'required' => false,
//                'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
//                'super_type' => $superType
//            ))
            ->add('genres1', 'entity',
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
                    'required' => false,
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
            )
            ->add('verticalBanner',
                'armd_media_file_type',
                array('required' => false,
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'medium',
                    'media_context' => 'lecture',
                    'with_remove' => true,
//                    'by_reference' => false
                )
            )
            ->add('horizontalBanner',
                'armd_media_file_type',
                array('required' => false,
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'medium',
                    'media_context' => 'lecture',
                    'with_remove' => true,
//                    'by_reference' => false
                )
            )
            ->add('tags', 'armd_tag', array('required' => false, 'attr' => array('class' => 'select2-tags')))
            ->add('recommended')
            ->add('showAtSlider')
            ->add('showAtFeatured')
            ->add('limitSliderForGenres', 'entity',
                array(
                    'class' => 'ArmdLectureBundle:LectureGenre',
                    'required' => false,
                    'multiple' => true,
                    'attr' => array('class' => 'chzn-select'),
                    'query_builder' => function (EntityRepository $er) use ($superType) {
                        return $er->createQueryBuilder('g')
                            ->where('g.level = 1')
                            ->andWhere('g.lectureSuperType = :lecture_super_type')
                            ->setParameter('lecture_super_type', $superType);
                    },
                )
            )
            ->add('limitFeaturedForGenres', 'entity',
                array(
                    'class' => 'ArmdLectureBundle:LectureGenre',
                    'required' => false,
                    'multiple' => true,
                    'attr' => array('class' => 'chzn-select'),
                    'query_builder' => function (EntityRepository $er) use ($superType) {
                        return $er->createQueryBuilder('g')
                            ->where('g.level = 1')
                            ->andWhere('g.lectureSuperType = :lecture_super_type')
                            ->setParameter('lecture_super_type', $superType);
                    },
                )
            )
            ->add('isTop100Film', null, array('required' => false))
            ->with('Главная')
                ->add('showOnMain', null, array(
                    'required' => false
                ))
                ->add('showOnMainOrd', null, array(
                    'required' => false
                ))
            ->end()
            /*->with('Tvigle Video')
                ->add('lectureVideo', 'armd_tvigle_video_selector', array( 'required' => false))
            ->end()*/
            ->with('Video')
                ->add('mediaLectureVideo', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lecture')))
                ->add('mediaTrailerVideo', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lecture')))
                ->add('series', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lecture')))
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
            ->add('title')
            ->add('genres')
            ->add('isTop100Film')
            ->add('showOnMain')
            ->add('showOnMainOrd')
            ->add('recommended')
            ->add('showAtSlider')
            ->add('showAtFeatured')
        ;
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
            ->add('showOnMain')
            ->add('showOnMainOrd')
            ->add('createdAt')
            ->add('genres', null, array('template' => 'ArmdLectureBundle:Admin:list_lecture_categories.html.twig'))
            ->add('isTop100Film')
            ->add('recommended')
            ->add('showAtSlider')
            ->add('showAtFeatured')
            ->add('limitSliderForGenres')
            ->add('limitFeaturedForGenres')
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
            $actions['ShowOnMain']=array(
                'label'            => $this->trans('aShowOnMain', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
            );
            $actions['NotShowOnMain']=array(
                'label'            => $this->trans('aNotShowOnMain', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
            );
            $actions['SetRecommended']=array(
                'label'            => 'Установить "Рекомендована"',
                'ask_confirmation' => false
            );
            $actions['ResetRecommended']=array(
                'label'            => 'Сбросить "Рекомендована"',
                'ask_confirmation' => false
            );
            $actions['SetShowAtSlider']=array(
                'label'            => 'Установить ' . $this->trans('Show At Slider'),
                'ask_confirmation' => false
            );
            $actions['ResetShowAtSlider']=array(
                'label'            => 'Сбросить ' . $this->trans('Show At Slider'),
                'ask_confirmation' => false
            );
            $actions['SetShowAtFeatured']=array(
                'label'            => 'Установить ' . $this->trans('Show At Featured'),
                'ask_confirmation' => false
            );
            $actions['ResetShowAtFeatured']=array(
                'label'            => 'Сбросить ' . $this->trans('Show At Featured'),
                'ask_confirmation' => false
            );
        }

        return $actions;
    }
    
    public function postPersist($object)
    {
        parent::postPersist($object);
        $this->saveTagging($object);
    }

    public function postUpdate($object)
    {
        parent::postUpdate($object);
        $this->saveTagging($object);
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    protected function saveTagging($object)
    {
        $this->container->get('fpn_tag.tag_manager')->saveTagging($object);
    }
}
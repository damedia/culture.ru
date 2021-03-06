<?php

namespace Armd\LectureBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class LectureAdmin extends Admin
{
    protected $translationDomain = 'ArmdLectureBundle';
    protected $baseRouteName = 'lecture';
    protected $baseRoutePattern = 'lecture';

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    public function createQuery($context = 'list')
    {
        $qb = $this->modelManager->getEntityManager('ArmdLectureBundle:Lecture')
            ->getRepository('ArmdLectureBundle:Lecture')->createQueryBuilder('l')
            ->innerJoin('l.lectureSuperType', 'st')
            ->where('st.code = \'LECTURE_SUPER_TYPE_LECTURE\'');

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
            ->add('genres')
            ->add('createdAt')
            ->add('lecturer')
            ->add('recommended')
            ->add('showAtSlider')
            ->add('showAtFeatured')
            ->add('trailerVideo')
            ->add('lectureVideo')
            ->add('mediaTrailerVideo')
            ->add('mediaLectureVideo');
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
            ->findOneByCode('LECTURE_SUPER_TYPE_LECTURE');

        if (!$lecture->getId()) {
            $lecture->setLectureSuperType($superType);

        } elseif ($lecture->getLectureSuperType()->getId() !== $superType->getId()) {
            throw new \RuntimeException('You can not edit video with type "' .$lecture->getLectureSuperType()->getName() .'" as video with type "' .$superType->getName() .'"');
        }

        $formMapper
            ->with('General')
                ->add('published')
                ->add('corrected', null, array('required' => false, 'disabled' => ($this->container->get('security.context')->isGranted('ROLE_CORRECTOR') ? false : true )))
                ->add('title')
                ->add('description', null, array(
                    'attr' => array('class' => 'tinymce'),
                ))
                ->add('genres', 'entity',
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
                ->add('verticalBanner',
                    'armd_media_file_type',
                    array('required' => false,
                        'media_provider' => 'sonata.media.provider.image',
                        'media_format' => 'medium',
                        'media_context' => 'lecture',
                        'with_remove' => true,
                    )
                )
                ->add('horizontalBanner',
                    'armd_media_file_type',
                    array('required' => false,
                        'media_provider' => 'sonata.media.provider.image',
                        'media_format' => 'medium',
                        'media_context' => 'lecture',
                        'with_remove' => true,
                    )
                )
                ->add('tags', 'armd_tag', array('required' => false, 'attr' => array('class' => 'select2-tags')))
                ->add('lecturer')
                ->add('timeLength')
                ->add('recommended')
                ->add('showAtSlider')
                ->add('showAtFeatured')
                ->add('showOnMainAsRecommended')
                ->add('showOnMainAsRecommendedFrom', 'date', array(
                        'required' => false
                    ))
                ->add('showOnMainAsRecommendedTo', 'date', array(
                        'required' => false
                    ))
            ->add('showOnMainAsRecommendedOrd')
            ->end()
            ->with('SEO')
                ->add('seoTitle', null, array('attr' => array('class' => 'span8')))
                ->add('seoKeywords')
                ->add('seoDescription')
            ->end()
            ->with('Video')
                ->add('mediaLectureVideo', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lecture')))
                ->add('mediaTrailerVideo', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lecture')))
                ->add('series', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lecture')))
            ->end()
            ->with('External Video')
                ->add('externalUrl', null, array('required' => false))
            ->end()
            ->with('Roles and persons')
                ->add('rolesPersons', 'sonata_type_model', array(
                    'label' => 'Roles',
                    'by_reference' => false,
                    'multiple' => true,
                    'expanded' => true,
                ))
            ->end()
            ->with('Stuff')
            ->add(
                'stuff',
                'collection',
                array(
                    'type' => 'armd_media_file_type',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'options' => array(
                        'required' => false,
                        'media_provider' => 'sonata.media.provider.file',
                        'by_reference' => true,
                        'media_context' => 'stuff',
                        'media_format' => 'original',
                        'with_remove' => false, // Удаление выше, на уровне коллекции.
                        'with_title' => false,
                        'with_description' => true,
                    ),
                    'attr' => array('class' => 'armd-sonata-images-collection'),
                )
            )
            ->end()
        ;
    }


    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('published')
            ->add('corrected')
            ->add('title')
            ->add('genres')
            ->add('lecturer')
            ->add('recommended')
            ->add('showAtSlider')
            ->add('showAtFeatured')
            ->add('showOnMainAsRecommended')
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
            ->add('corrected')
            ->add('createdAt')
            ->add('lectureType')
            ->add('genres', null, array('template' => 'ArmdLectureBundle:Admin:list_lecture_categories.html.twig'))
            ->add('lecturer')
            ->add('recommended')
            ->add('showAtSlider')
            ->add('showAtFeatured')
            ->add('showOnMainAsRecommended', null, array('editable' => true))
        ;
    }

    public function getFormTheme()
    {
        $themes = parent::getFormTheme();
        $themes[] = 'ArmdLectureBundle:Form:fields.html.twig';
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

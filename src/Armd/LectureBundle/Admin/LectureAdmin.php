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
            ->add('categories')
            ->add('genres')
            ->add('createdAt')
            ->add('lecturer')
            ->add('recommended')
            ->add('trailerVideo')
            ->add('lectureVideo');
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

        $lecture->setLectureSuperType($superType);


        $formMapper
            ->with('General')
                ->add('published')
                ->add('title')
                ->add('description', null, array(
                    'attr' => array('class' => 'tinymce'),
                ))
                ->add('categories', 'armd_lecture_categories',
                    array(
                        'required' => false,
                        'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
                        'super_type' => $superType
                    )
                )
                ->add('genres', 'entity',
                    array(
                        'class' => 'ArmdLectureBundle:LectureGenre',
                        'multiple' => 'true',
                        'query_builder' => function (EntityRepository $er) use ($superType) {
                            return $er->createQueryBuilder('g')
                                ->where('g.level = 1')
                                ->andWhere('g.lectureSuperType = :lecture_super_type')
                                ->setParameter('lecture_super_type', $superType);
                        }
                    )
                )
                ->add('genres', 'entity',
                    array(
                        'class' => 'ArmdLectureBundle:LectureGenre',
                        'multiple' => 'true',
                        'query_builder' => function (EntityRepository $er) use ($superType) {
                            return $er->createQueryBuilder('g')
                                ->where('g.level = 2')
                                ->andWhere('g.lectureSuperType = :lecture_super_type')
                                ->setParameter('lecture_super_type', $superType);
                        }
                    )
                )
                ->add('tags', 'armd_tag', array('required' => false, 'attr' => array('class' => 'select2-tags')))
                ->add('lecturer')
                ->add('recommended')
            ->end()
            ->with('SEO')
                ->add('seoTitle')
                ->add('seoKeywords')
                ->add('seoDescription')
            ->end()
            ->with('Tvigle Video')
                ->add('trailerVideo', 'armd_tvigle_video_selector',
                    array(
                        'required' => false
                    )
                )
                ->add('lectureVideo', 'armd_tvigle_video_selector',
                    array(
                        'required' => false
                    )
                )
            ->end()
            ->with('Other video')
                ->add('mediaLectureVideo', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lecture')))
                ->add('mediaTrailerVideo', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lecture')))
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
            ->add('title')
            ->add('categories')
            ->add('lecturer');
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
            ->add('createdAt')
            ->add('lectureType')
            ->add('categories', null, array('template' => 'ArmdLectureBundle:Admin:list_lecture_categories.html.twig'))
            ->add('lecturer');
    }

    public function getFormTheme()
    {
        $themes = parent::getFormTheme();
        $themes[] = 'ArmdLectureBundle:Form:fields.html.twig';
        return $themes;
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

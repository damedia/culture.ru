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
            ->add('lectureType')
            ->add('categories')
            ->add('createdAt')
            ->add('lecturer')
            ->add('recommended')
            ->add('trailerVideo')
            ->add('lectureVideo')
            ->add('lectureFile');
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
            ->add('published')
            ->add('title')
            ->add('description')
            ->add('lectureType')
            ->add('categories', 'armd_lecture_categories',
                array(
                    'required' => false,
                    'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
                    'super_type' => $superType
                )
            )
            ->add('lecturer')
            ->add('recommended')
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
            ->add('lectureFile', 'armd_media_file_type',
                array(
                    'required' => false,
                    'with_remove' => true,
                    'media_context' => 'lecture',
                    'media_provider' => 'sonata.media.provider.file',
                    'media_format' => 'default'
                )
            );
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
        $themes[] = 'ArmdMediaHelperBundle:Form:fields.html.twig';
        return $themes;
    }

}
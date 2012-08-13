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

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('lectureType')
            ->add('categories')
            ->add('createdAt')
            ->add('lecturer')
            ->add('recommended')
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
        $formMapper
            ->add('title')
            ->add('lectureType')
            ->add('categories', 'armd_lecture_categories',
            array(
                'required' => false,
                'attr' => array('class' => 'chzn-select atlas-object-categories-select')
            ))
            ->add('lecturer')
            ->add('recommended')
            ->add('lectureVideo', 'armd_tvigle_video_selector',
            array(
                'required' => false
            ))
            ->add('lectureFile', 'armd_media_file_type',
            array(
                'media_provider' => 'sonata.media.provider.file',
                'media_context' => 'lecture',
                'required' => false,
                'by_reference' => false,
                'with_remove' => true
            ));
    }


    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
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
            ->add('createdAt')
            ->add('lectureType')
            ->add('categories')
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
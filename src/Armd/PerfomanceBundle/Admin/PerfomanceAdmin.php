<?php

namespace Armd\PerfomanceBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class PerfomanceAdmin extends Admin
{
    protected $translationDomain = 'ArmdPerfomanceBundle';
    protected $baseRouteName = 'perfomance';
    protected $baseRoutePattern = 'perfomance';

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

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
            ->add('year')
            ->add('ganres')
            ->add('trailerVideo')
            ->add('perfomanceVideo');
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
                ->add('published')
                ->add('corrected', null, array('required' => false, 'disabled' => ($this->container->get('security.context')->isGranted('ROLE_CORRECTOR') ? false : true )))
                ->add('title')
                ->add('year')
                ->add('description', null, array(
                    'attr' => array('class' => 'tinymce'),
                ))
                ->add('ganres', 'entity',
                    array(
                        'required' => true,
						'class' => 'Armd\PerfomanceBundle\Entity\PerfomanceGanre',
						'property' => 'title',
						'multiple' => true
                    )
                )
                ->add('theater', null, array(
                    'property' => 'title',
                    'label' => 'Theater',
                    'attr' => array('class' => 'chzn-select span5'),
                    'query_builder' => function($er) {
                        $qb = $er->createQueryBuilder('r');
                        $qb->orderBy('r.title', 'ASC');
                        return $qb;
                    }
                ))
                ->add('tags', 'armd_tag', array('required' => false, 'attr' => array('class' => 'select2-tags')))
            ->end()
            /*->with('Tvigle Video')
                ->add('trailerVideo', 'armd_tvigle_video_selector',
                    array(
                        'required' => false
                    )
                )
                ->add('perfomanceVideo', 'armd_tvigle_video_selector',
                    array(
                        'required' => false
                    )
                )*/
            ->with('Video')
                ->add('mediaTrailerVideo', 'sonata_type_model_list',
                    array('required' => false),
                    array('link_parameters' => array(
                        'context'  => 'perfomance',
                        'provider' => 'sonata.media.provider.tvigle'
                    ))
                )
                ->add('mediaPerfomanceVideo', 'sonata_type_model_list',
                    array('required' => false),
                    array('link_parameters' => array(
                        'context'  => 'perfomance',
                        'provider' => 'sonata.media.provider.tvigle'
                    ))
                )
            ->end()
            ->with('External')
                ->add('externalUrl', 'url',  array('required' => false))
				->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'perfomance')))
            ->end()
            ->with('Interview')
                /*->add('interviewVideo', 'armd_tvigle_video_selector',
                    array(
                        'required' => false
                    )
                )*/
                ->add('mediaInterviewVideo', 'sonata_type_model_list',
                    array('required' => false),
                    array('link_parameters' => array(
                        'context'  => 'perfomance',
                        'provider' => 'sonata.media.provider.tvigle'
                    ))
                )
				->add('interviewTitle')
                ->add('interviewDescription', null, array(
                    'attr' => array('class' => 'tinymce'),
                ))
            ->end()
            ->with('Media')
                ->add('gallery', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'perfomance')))
            ->end()
            ->with('Главная')
                ->add('showOnMain')
                ->add('showOnMainFrom', 'date', array(
                        'required' => false
                    ))
                ->add('showOnMainTo', 'date', array(
                        'required' => false
                    ))
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
            ->add('year')
            ->add('ganres')
            ->add('showOnMain')
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
            ->add('year')
            ->add('published')
            ->add('corrected')
            ->add('createdAt')
            ->add('ganres')
            ->add('showOnMain', null, array('editable' => true))
        ;
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

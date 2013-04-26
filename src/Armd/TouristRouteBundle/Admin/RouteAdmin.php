<?php

namespace Armd\TouristRouteBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;
use Armd\TouristRouteBundle\Entity\Category;

class RouteAdmin extends Admin
{
    protected $translationDomain = 'ArmdTouristRouteBundle';

    protected $container;

    public function __construct($code, $class, $baseControllerName, $serviceContainer)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $serviceContainer;
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
            ->add('content')
            ->add('categories')
            ->add('regions')
            ->add('objects')
            ->add('points')
            ->add('images')
            ->add('videos')
            ->add('routes')
            ->add('banner');
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
                ->add('title')
                ->add('content', null, array(
                    'attr' => array('class' => 'tinymce'),
                ))
                ->add('categories', 'sonata_type_model', array(
                    'multiple' => true,
                    'expanded' => true,
                    'required' => true
                ))
            ->end()
            ->with('Geo')
                ->add('regions', 'armd_entity_ajax', array(
                    'required' => true,
                    'multiple' => true,
                    'class'    => 'ArmdAtlasBundle:Region',
                    'property' => 'title',
                    'configs'  => array(
                        'width'       => 470,
                        'placeholder' => $this->trans('Select regions')
                    )
                ))
                ->add('objects', 'armd_entity_ajax', array(
                    'required' => false,
                    'multiple' => true,
                    'class'    => 'ArmdAtlasBundle:Object',
                    'property' => 'title',
                    'configs'  => array(
                        'width'       => 470,
                        'placeholder' => $this->trans('Select objects')
                    )
                ))
                ->add('type', 'choice', array(
                    'choices' => array(
                        'route'    => $this->trans('Route'),
                        'polyline' => $this->trans('Polyline')
                    )
                ))
                ->add('points', 'armd_point_collection', array(
                    'allow_add' => true,
                    'allow_delete' => true
                ))
            ->end()
            ->with('Media')
                ->add('images', 'collection', array(
                    'type'    => 'armd_media_file_type',
                    'options' => array(
                        'media_context'  => 'route',
                        'media_provider' => 'sonata.media.provider.image',
                        'media_format'   => 'thumbnail',
                        'with_title'     => true,
                    ),
                    'by_reference' => false,
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'required'     => false,
                    'attr'         => array('class' => 'armd-sonata-images-collection'),
                ))
                ->add('videos', 'collection', array(
                    'type' => 'armd_tvigle_video_selector',
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required'     => false,
                    'attr' => array('class' => 'armd-sonata-tvigle-collection'),
                    'options' => array('attr' => array('class' => 'armd-sonata-tvigle-form')),
                ))
            ->end()
            ->with('Recommendations')
                ->add('routes', 'armd_entity_ajax', array(
                    'required' => false,
                    'multiple' => true,
                    'class'    => 'ArmdTouristRouteBundle:Route',
                    'property' => 'title',
                    'configs'  => array(
                        'width'       => 470,
                        'placeholder' => $this->trans('Select routes')
                    )
                ))
                ->add('banner', 'armd_media_file_type', array(
                    'required' => false,
                    'with_remove' => true,
                    'media_context' => 'route',
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'thumbnail'
                ))
            ->end()
        ;

        parent::configureFormFields($formMapper);
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
            ->add('categories')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('published')
            ->add('categories');
    }

    public function getBatchActions()
    {
        $actions = array_merge(
            parent::getBatchActions(),
            array(
                'publish'   => array('label' => $this->trans('Publish')),
                'unpublish' => array('label' => $this->trans('Unpublish'))
            )
        );

        return $actions;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        if (!$this->getSubject()->getImages()->count()) {
            $errorElement->addViolation($this->trans('You must select at least one image'));
        }
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

    protected function saveTagging($object)
    {
        $this->container->get('fpn_tag.tag_manager')->saveTagging($object);
    }
}

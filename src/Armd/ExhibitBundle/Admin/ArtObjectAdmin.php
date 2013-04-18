<?php

namespace Armd\ExhibitBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class ArtObjectAdmin extends Admin
{    
    protected $container;

    public function __construct($code, $class, $baseControllerName, $container)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;
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
            ->add('textDate')
            ->add('date')
            ->add('description')
            ->add('image')   
            ->add('authors')
            ->add('museum')           
            ->add('videos')
            ->add('categories')
        ;
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
                ->add('textDate')
                ->add('date', null, array('widget' => 'single_text'))
                ->add('description')  
                ->add('authors', null,
                    array(
                        'required' => false,
                        'attr' => array('class' => 'chzn-select span5'),
                        'query_builder' => function($er) {
                            $qb = $er->createQueryBuilder('a');
                            $qb->join('a.personTypes', 't');
                            $qb->andWhere('t.slug = :slug')->setParameter('slug', 'art_gallery_author');
                            return $qb;
                        }
                    )
                )
                ->add('museum', null,
                    array(
                        'required' => false,
                        'attr' => array('class' => 'chzn-select span5')
                    )
                )
                ->add('categories', 'armd_art_object_categories', array(
                    'required' => true,
                    'attr' => array('class' => 'chzn-select span5')
                ))
                ->add('tags', 'armd_tag', array(
                    'required' => false,
                    'attr' => array('class' => 'select2-tags'),
                ))
            ->end()
            ->with('Media')
                ->add('image', 'armd_media_file_type', array( 
                    'required' => false,
                    'with_remove' => true,
                    'media_context' => 'exhibit',
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'small'
                ))
                ->add('videos', 'collection', array(
                    'required' => false,
                    'type' => 'armd_tvigle_video_selector',
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'attr' => array('class' => 'armd-sonata-tvigle-collection'),
                    'options' => array('attr' => array('class' => 'armd-sonata-tvigle-form')),
                    'label' => 'Видео (Tvigle ID)'
                ))
            ->end()
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
            ->add('date')
            ->add('published')
        ;
    }
    
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('categories', null, array(    
                'field_options' => array(
                    'query_builder' => function($er) {
                        $qb = $er->createQueryBuilder('c');
                        $qb->orderBy('c.root, c.lft', 'ASC');
                        return $qb;
                    }
                )
            ))
        ;
    }
    
    public function getFormTheme() {
        $themes = parent::getFormTheme();
        $themes[] = 'ArmdExhibitBundle:Form:fields.html.twig';
        return $themes;
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['publish'] = array(
            'label' => 'Публиковать'
        );
        $actions['unpublish'] = array(
            'label' => 'Снять публикацию'
        );

        return $actions;
    }
}
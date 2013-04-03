<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\TheaterBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class TheaterAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_by'      => 'title',    
        '_sort_order'   => 'ASC',
    );

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
            ->add('director')
            ->add('address')
            ->add('url')
            ->add('email')
            ->add('phone')
            ->add('image')
        ;
        
        parent::configureShowFields($showMapper);
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
                ->add('city', null, array(
                    'required' => true,
                    'property' => 'title',
                    'label' => 'City',
                    'attr' => array('class' => 'chzn-select span5'),
                    'query_builder' => function($er) {
                        $qb = $er->createQueryBuilder('r');
                        $qb->orderBy('r.title', 'ASC');
                        return $qb;
                    }
                ))
                ->add('categories', null, array('attr' => array('class' => 'chzn-select span5')))
                ->add('description')
                ->add('director')
                ->add('address')
                ->add('url')
                ->add('email')
                ->add('phone')                
            ->end()                      
            ->with('Media')
                ->add('image', 'armd_media_file_type', array(
                    'required' => false,
                    'with_remove' => true,
                    'media_context' => 'theater',
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'thumbnail'
                ))               
                ->add('images', 'collection', array(
                    'type' => 'armd_media_file_type',
                    'options' => array(
                        'media_context' => 'theater',
                        'media_provider' => 'sonata.media.provider.image',
                        'media_format' => 'thumbnail'
                    ),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => false,
                    'attr' => array('class' => 'armd-sonata-images-collection'),
                ))
            ->end()
            ->with('Tvigle Video')
                ->add('interview', 'armd_tvigle_video_selector',
                    array(
                        'required' => false
                    )
                )               
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
        ;
        
        parent::configureListFields($listMapper);        
    }
    
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('published')
            ->add('title')
            ->add('city')
        ;
    }        
}

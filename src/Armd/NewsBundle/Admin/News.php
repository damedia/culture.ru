<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\NewsBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class News extends Admin
{
    protected $translationDomain = 'ArmdNewsBundle';

    protected $datagridValues = array(
        '_sort_by'      => 'date',    
        '_sort_order'   => 'DESC',
    );

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('announce')
            ->add('body')
            ->add('date')                            
        ;
        
        parent::configureShowField($showMapper);        
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
                ->add('title')
                ->add('announce')
                ->add('body')
                ->add('date', null, array('date_widget' => 'single_text', 'time_widget' => 'single_text'))
                ->add('endDate', null, array('date_widget' => 'single_text', 'time_widget' => 'single_text'))                
                ->add('category')
                ->add('subject', null, array('required' => false))                
                ->add('source')                
                ->add('important', null, array('required' => false))
                ->add('priority')                                
                ->add('published', null, array('required' => false))
            ->end()    
            ->with('Media')                
                ->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'news')))
                ->add('gallery', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'gallery')))
                ->add('video', 'armd_tvigle_video_selector', array('required' => false))
            ->end()
            ->with('Map')
                ->add('isOnMap', null, array('required' => false))
                ->add('lat')
                ->add('lon')
            ->end();

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
            ->add('date')            
            ->add('category')
            ->add('subject')            
            ->add('important')                  
            ->add('published')                            
        ;
        
        parent::configureListFields($listMapper);        
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('category')
            ->add('subject')            
            ->add('published')
            ->add('important')            
        ;
    }
}

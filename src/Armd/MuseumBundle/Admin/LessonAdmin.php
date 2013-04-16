<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\MuseumBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;


class LessonAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_by'      => 'date',    
        '_sort_order'   => 'DESC',
    );

    protected $translationDomain = 'ArmdMuseumBundle';

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('createdAt')
            ->add('published')
            ->add('image')
            ->add('city')
            ->add('museum')
            ->add('dates')
            ->add('time')
            ->add('maxMembers')
            ->add('place')
            ->add('education')
            ->add('subject')
            ->add('skills')
            ->add('age')
            ->add('format')
            ->add('url')
            ->add('announce')
            ->add('description')
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
            	->add('published')
                ->add('title')
	            ->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'lesson')))
	            ->add('city')
	            ->add('museum')
	            ->add('dates')
	            ->add('time')
	            ->add('maxMembers')
	            ->add('place')
	            ->add('education')
	            ->add('subject')
	            ->add('skills')
	            ->add('age')
	            ->add('format')
	            ->add('url', 'url', array('required' => false))
	            ->add('announce')
                ->add('description', null, array(
                    'attr' => array('class' => 'tinymce'),
                ))
                ->add('tags', 'armd_tag', array('required' => false, 'attr' => array('class' => 'select2-tags')))            
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
            ->add('createdAt')
            ->add('published')  
	        ->add('city')
	        ->add('museum')
	        ->add('dates')                    
        ;
        
        parent::configureListFields($listMapper);        
    }    
    
    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('published')
            ->add('title')
            ->add('city')
            ->add('museum')
            ->add('subject')
            ->add('skills')
        ;
    }    
}

<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\ChronicleBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class Event extends Admin
{
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
//            ->add('announce')
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
                ->add('body')
                ->add('date', null, array('widget' => 'single_text'))
//                ->add('decade')
//                ->add('year')                                                    
//                ->add('accidents', 'sonata_type_collection', array('required' => false, 'by_reference' => false), array('edit' => 'inline', 'inline' => 'table'))                
//                ->add('priority')                                
                ->add('published')
            ->end()    
            ->with('Media')                
                ->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'chronicle')))
                ->add('gallery', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'chronicle')))                                
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
            ->add('published')                            
        ;
        
        parent::configureListFields($listMapper);        
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('published')
            ->add('century')            
        ;
    }
}

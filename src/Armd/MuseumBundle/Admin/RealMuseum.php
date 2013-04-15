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

class RealMuseum extends Admin
{
    protected $datagridValues = array(
        '_sort_by'      => 'title',    
        '_sort_order'   => 'ASC',
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
            ->add('address')
            ->add('url')
            ->add('description')
            ->add('image')
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
                ->add('address')
                ->add('url', 'url', array('required' => false))
                ->add('description')
                ->add('category')
                ->add('region', null, array(
                    'required' => false,
                    'property' => 'title',
                    'label' => 'Region',
                    'attr' => array('class' => 'chzn-select span5'),
                    'query_builder' => function($er) {
                        $qb = $er->createQueryBuilder('r');
                        $qb->orderBy('r.title', 'ASC');
                        return $qb;
                    }
                ))
                ->add('tags', 'armd_tag', array('required' => false, 'attr' => array('class' => 'select2-tags')))             
            ->end()
            ->with('Images of Russia')
                ->add('atlasObject', null, array(
                    'required' => false,
                    'property' => 'title',
                    'label' => 'Atlas Object',
                    'attr' => array('class' => 'chzn-select span5'),
                    'query_builder' => function($er) {
                        $qb = $er->createQueryBuilder('o');
                        $qb->orderBy('o.title', 'ASC');
                        return $qb;
                    }
                ))
            ->end()
            ->with('Virtual Tour')
                ->add(
                    'virtualTours',
                    null,
                    array(
                        'required' => false,
                        'attr' => array('class' => 'chzn-select real-museum-virtual-tours-select')
                    )
                )
            ->end()
            ->with('Media')
                ->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'museum')))
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
            ->add('category')         
            ->add('region')
        ;
        
        parent::configureListFields($listMapper);        
    }  
    
    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('region')
            ->add('category')
        ;
    }      
}

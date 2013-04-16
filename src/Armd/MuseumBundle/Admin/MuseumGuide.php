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

class MuseumGuide extends Admin
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
            ->add('city')
            ->add('museum')
            ->add('announce')
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
                ->add('title')
                ->add('announce')
                ->add('body')
                ->add('city')
                ->add('museum')
            ->end()
            ->with('Media')
                ->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'museum_guide', 'provider' => 'sonata.media.provider.image')))
                ->add('file', 'sonata_type_model_list', array(), array('link_parameters'=>array('context'=>'museum_guide', 'provider' => 'sonata.media.provider.file')))
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
            ->add('city')
            ->add('museum')
            ->add('announce')
        ;
        
        parent::configureListFields($listMapper);        
    }
    
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('city')
            ->add('museum')
        ;
    }    
}
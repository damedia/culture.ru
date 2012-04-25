<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\EventBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Armd\Bundle\NewsBundle\Admin\NewsAdmin as BaseAdmin;

use Knp\Menu\ItemInterface as MenuItemInterface;

class EventAdmin extends BaseAdmin
{
    protected function configureShowField(ShowMapper $showMapper)
    {
        parent::configureShowField($showMapper);
        
        $showMapper
            ->add('place')
            ->remove('date')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);
        
        $formMapper
            ->with('General')
                ->remove('date')
                ->add('place')
                ->add('personalTag')
                ->add('tags')                
                ->add('showInCollage')                
                ->add('image', 'sonata_type_model', array(), array('edit'=>'list', 'link_parameters'=>array('context'=>'default')))
                ->add('collageImage', 'sonata_type_model', array(), array('edit'=>'list', 'link_parameters'=>array('context'=>'default')))
                ->add('gallery', 'sonata_type_model', array(), array('edit'=>'list', 'link_parameters'=>array('context'=>'default')))                
            ->end();
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);
        
        $listMapper
            ->add('place')
            ->remove('beginDate')
            ->remove('endDate')
        ;
    }
}
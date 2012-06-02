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

class ScheduleAdmin extends Admin
{
    protected function configureShowField(ShowMapper $showMapper)
    {
        parent::configureShowField($showMapper);
        
        $showMapper
            ->add('beginDate')
            ->add('endDate')
            ->add('event')
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
                ->add('beginDate')
                ->add('endDate', null, array('required'=>false))
                ->add('event')
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
            ->addIdentifier('beginDate')
            ->add('endDate')
            ->add('event')
        ;
    }
}
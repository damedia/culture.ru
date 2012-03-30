<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\CultureMapBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Knp\Menu\ItemInterface as MenuItemInterface;

class SubjectAdmin extends Admin
{
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
            ->add('title')
            ->add('announce')
            ->add('text')
            ->add('square')
            ->add('cityCount')
            ->add('villageCount')
            ->add('cityPopulation')
            ->add('administrativeCenter')
            ->add('nationality')
            ->add('language')
            ->add('religion')
            ->add('logo', 'sonata_type_model', array(), array('edit'=>'list', 'link_parameters'=>array('context'=>'default')))
            ->add('gallery', 'sonata_type_model', array(), array('edit'=>'list', 'link_parameters'=>array('context'=>'default')))
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
            ->add('announce')
        ;
    }
}
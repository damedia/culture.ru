<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\CommonBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class PersonAdmin extends BaseAdmin
{
    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('lastname')
                ->add('firstname')
                ->add('middlename')
                ->add('birthpalce')
                ->add('birthday', null, array('required'=>false))
                ->add('birthdayDescription')
                ->add('deathday', null, array('required'=>false))
                ->add('deathdayDescription')                
                ->add('occupation')
            ->end()
        ;

        parent::configureFormFields($formMapper);

        $formMapper
            ->with('General')
                ->remove('title')
            ->end()
        ;
    }
}

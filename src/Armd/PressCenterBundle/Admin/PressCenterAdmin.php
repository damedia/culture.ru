<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\PressCenterBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class PressCenterAdmin extends Admin
{
    protected $translationDomain = 'PressCenterBundle';
    protected $container;


    public function __construct($code, $class, $baseControllerName, $container)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;
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
            ->add('slug')
            ->add('title')
            ->add(
                'content',
                null,
                array(
                    'attr' => array('class' => 'tinymce'),
                )
            )
            ->end()
            ->with('Главная')
            ->add('showOnMain', null, array(
                    'required' => false
                ))
            ->add('showOnMainFrom', 'date', array(
                    'required' => false
                ))
            ->add('showOnMainTo', 'date', array(
                    'required' => false
                ))
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
            ->add('slug')
            ->addIdentifier('title')
            ->add('createdAt')
            ->add('updatedAt');

        parent::configureListFields($listMapper);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('slug')
            ->add('title')
        ;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'ArmdNewsBundle:Form:edit_container.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

}

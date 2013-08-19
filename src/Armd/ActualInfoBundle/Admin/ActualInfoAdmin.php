<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\ActualInfoBundle\Admin;

use Armd\ActualInfoBundle\Entity\ActualInfo;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class ActualInfoAdmin extends Admin
{
    protected $translationDomain = 'ActualInfoBundle';
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
        $choices = array(
            ActualInfo::TYPE_TEXT => 'Текст',
            ActualInfo::TYPE_IMAGE => 'Картинка',
            ActualInfo::TYPE_VIDEO => 'Видео',
        );

        $formMapper
            ->with('General')
            ->add('type', 'choice', array('choices' => $choices))
            ->add(
                'text',
                null,
                array(
                    'attr' => array('class' => 'tinymce'),
                )
            )
            ->add('image', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'actual_info')))
            ->add('video', 'sonata_type_model_list', array('required' => false), array('link_parameters'=>array('context'=>'actual_info')))
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
            ->addIdentifier('type')
            ->add('text')
            ->add('showOnMain', null, array('editable' => true))
            ->add('createdAt')
            ->add('updatedAt');

        parent::configureListFields($listMapper);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('type')
            ->add('text')
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

<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\BlogBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class Blog extends Admin
{
    protected $translationDomain = 'ArmdBlogBundle';
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
        $isNew = ($this->getSubject() && !$this->getSubject()->getId());
        $formMapper
            ->with('General')
            ->add('title')
            ->add(
                'lead',
                null,
                array(
                    'attr' => array('class' => 'tinymce'),
                )
            )
            ->add(
                'content',
                null,
                array(
                    'attr' => array('class' => 'tinymce'),
                )
            )
            ->add('description')
            ->add(
                'topImage',
                'armd_media_file_type',
                array(
                    'required' => $isNew,
                    'with_remove' => false,
                    'media_context' => 'blog_image',
                    'media_provider' => 'sonata.media.provider.image',
                )
            )
            ->add(
                'user',
                'blogger_type',
                array(
                    'required' => true,
                    'multiple' => false,
                    'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
                )
            )
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
            ->add('user')
            ->add('created_at')
            ->add('updated_at');

        parent::configureListFields($listMapper);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title');
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

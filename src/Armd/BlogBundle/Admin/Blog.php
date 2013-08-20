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
    protected $translationDomain = 'BlogBundle';
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
            ->add('title', null, array('label' => 'Заголовок'))
            ->add(
                'lead',
                null,
                array(
                    'attr' => array('class' => 'tinymce'),
                    'label' => 'Лид'
                )
            )
            ->add(
                'content',
                null,
                array(
                    'attr' => array('class' => 'createPage_blockTextarea tinymce',
                                    'data-theme' => 'sproject_snippets'),
                    'label' => 'Содержание'
                )
            )
            ->add(
                'topImage',
                'armd_media_file_type',
                array(
                    'label' => 'Картинка',
                    'required' => $isNew,
                    'with_remove' => false,
                    'media_context' => 'blog_image',
                    'media_provider' => 'sonata.media.provider.image',
                )
            )
            ->add('description', null, array('label' => 'Описание для картинки (минимальная ширина картинки 698px)'))
            ->add(
                'user',
                'blogger_type',
                array(
                    'label' => 'Автор',
                    'required' => true,
                    'multiple' => false,
                    'attr' => array('class' => 'chzn-select atlas-object-categories-select'),
                )
            )
            ->end();

        $formMapper->setHelps(array(
                'lead' => 'Короткое описание, которое будет выводиться под заголовком на странице блогов или в блоках Популярные, Последние',
                'topImage' => 'Картинка, которая отображается на странице блогов у первого блога, а также сверху страницы блога',
                'description' => 'Короткое описание, которое отображается под заглавной картинкой на странице записи блога'
            ));

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
            ->addIdentifier('title', null, array('label' => 'Заголовок'))
            ->add('user', null, array('label' => 'Автор'))
            ->add('created_at', null, array('label' => 'Дата создания'))
            ->add('updated_at', null, array('label' => 'Дата обновления'));

        parent::configureListFields($listMapper);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'Заголовок'));
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'edit':
                return 'BlogBundle:Admin:novalidate_edit_form.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    public function prePersist($object)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $snippetParser = $container->get('special_project_snippet_parser');
        $tmp = $object->getLead();
        $snippetParser->html_to_entities($tmp);
        $object->setLead($tmp);
        $tmp = $object->getContent();
        $snippetParser->html_to_entities($tmp);
        $object->setContent($tmp);
    }

    public function preUpdate($object)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $snippetParser = $container->get('special_project_snippet_parser');
        $tmp = $object->getLead();
        $snippetParser->html_to_entities($tmp);
        $object->setLead($tmp);
        $tmp = $object->getContent();
        $snippetParser->html_to_entities($tmp);
        $object->setContent($tmp);
    }

}

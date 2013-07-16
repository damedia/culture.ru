<?php
namespace Damedia\SpecialProjectBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Sonata\AdminBundle\Route\RouteCollection;

class PageAdmin extends Admin {
    const LABEL_ID = 'ID';
    const LABEL_TITLE = 'Название страницы';
    const LABEL_SLUG = 'Суффикс URL';
    const LABEL_CREATED = 'Дата создания';
    const LABEL_UPDATED = 'Дата изменения';
    const LABEL_IS_PUBLISHED = 'Опубликован';
    const LABEL_TEMPLATE_ID = 'Шаблон';

    const LABEL_ACTIONS = 'Управление';



    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('title', null,
            array('label' => $this::LABEL_TITLE));

        $formMapper->add('slug', null,
            array('label' => $this::LABEL_SLUG,
                  'required' => false));

        $formMapper->add('template', 'entity',
            array('label' => $this::LABEL_TEMPLATE_ID,
                  'class' => 'DamediaSpecialProjectBundle:Template',
                  'property' => 'title'));

        $formMapper->add('isPublished', null,
            array('label' => $this::LABEL_IS_PUBLISHED,
                  'required' => false));
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->add('id', null,
            array('label' => $this::LABEL_ID));

        $listMapper->addIdentifier('title', null,
            array('label' => $this::LABEL_TITLE));

        $listMapper->add('_action', 'actions',
            array('label' => $this::LABEL_ACTIONS,
                  'actions' => array('previewPage' => array('template' => 'DamediaSpecialProjectBundle:Admin:pageAdmin_previewPage.html.twig'),
                                     'editPage' => array('template' => 'DamediaSpecialProjectBundle:Admin:pageAdmin_editPage.html.twig'),
                                     'delete' => array('template' => 'DamediaSpecialProjectBundle:Admin:pageAdmin_deletePage.html.twig'))));

        $listMapper->add('slug', null,
            array('label' => $this::LABEL_SLUG));

        $listMapper->add('created', null,
            array('label' => $this::LABEL_CREATED));

        $listMapper->add('updated', null,
            array('label' => $this::LABEL_UPDATED));

        $listMapper->add('isPublished', null,
            array('label' => $this::LABEL_IS_PUBLISHED));

        $listMapper->add('template', null,
            array('label' => $this::LABEL_TEMPLATE_ID));
    }

    protected function configureRoutes(RouteCollection $collection) {
        $collection->add('previewPage', $this->getRouterIdParameter().'/previewpage');
        $collection->add('editPage', $this->getRouterIdParameter().'/editpage');
    }
}
?>
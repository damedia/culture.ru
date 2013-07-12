<?php
namespace Damedia\SpecialProjectBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BlockAdmin extends Admin {
    const LABEL_ID = 'ID';
    const LABEL_PAGE = 'Страница';
    const LABEL_PLACEHOLDER = 'Placeholder';



    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('page', null,
            array('label' => $this::LABEL_PAGE));

        $formMapper->add('placeholder', null,
            array('label' => $this::LABEL_PLACEHOLDER));
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->add('id', null,
            array('label' => $this::LABEL_ID));

        $listMapper->addIdentifier('page', null,
            array('label' => $this::LABEL_PAGE));

        $listMapper->add('placeholder', null,
            array('label' => $this::LABEL_PLACEHOLDER));
    }
}
?>
<?php
namespace Damedia\SpecialProjectBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ChunkAdmin extends Admin {
    const LABEL_ID = 'ID';
    const LABEL_BLOCK = 'Блок';
    const LABEL_CONTENT = 'Содержимое';



    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('block', null,
            array('label' => $this::LABEL_BLOCK));

        $formMapper->add('content', null,
            array('label' => $this::LABEL_CONTENT));
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->add('id', null,
            array('label' => $this::LABEL_ID));

        $listMapper->addIdentifier('block', null,
            array('label' => $this::LABEL_BLOCK));

        $listMapper->add('content', null,
            array('label' => $this::LABEL_CONTENT));
    }
}
?>
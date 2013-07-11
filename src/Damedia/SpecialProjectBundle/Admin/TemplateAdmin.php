<?php
namespace Damedia\SpecialProjectBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class TemplateAdmin extends Admin {
    const LABEL_ID = 'ID';
    const LABEL_TITLE = 'Название шаблона';
    const LABEL_TWIG_FILE_NAME = 'Twig-файл';



    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('title', null,
                         array('label' => $this::LABEL_TITLE));

        $formMapper->add('twigFileName', 'choice',
                         array('label' => $this::LABEL_TWIG_FILE_NAME,
                               'help' => 'twig-файл разметки шаблона (файлы находятся в ../src/Damedia/SpecialProjectBundle/Resources/views/Templates/)',
                               'required' => false,
                               'choices' => $this->getTwigFilesList()));
    }

    protected function configureListFields(ListMapper $listMapper) {
        $listMapper->add('id', null,
                         array('label' => $this::LABEL_ID));

        $listMapper->addIdentifier('title', null,
                         array('label' => $this::LABEL_TITLE));

        $listMapper->add('twigFileName', null,
                         array('label' => $this::LABEL_TWIG_FILE_NAME));
    }



    private function getTwigFilesList() {
        $result = array('' => '');

        $container = $this->getConfigurationPool()->getContainer();
        $kernel = $container->get('kernel');
        $templatesDirectory = $kernel->locateResource('@DamediaSpecialProjectBundle/Resources/views/Templates');

        foreach (scandir($templatesDirectory) as $fileName) {
            if ($fileName !== '.' AND $fileName !== '..') {
                $result[$fileName] = $fileName;
            }
        }

        return $result;
    }
}
?>
<?php
namespace Damedia\SpecialProjectBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\HttpKernel\Kernel;

class TemplateAdmin extends Admin {
    protected function configureFormFields(FormMapper $formMapper) {
        $formMapper->add('title', 'text',
                         array('label' => 'Название шаблона'));

        $formMapper->add('twigFileName', 'choice',
                         array('label' => 'twig-файл',
                               'help' => 'имя twig-файла, описывающего разметку шаблона (фалы разметки лежат в [путь к файлам]',
                               'choices' => $this->getTwigFilesToChoose()));
    }

    private function getTwigFilesToChoose() {
        $result = array();

        $container = $this->getConfigurationPool()->getContainer();
        $kernel = $container->get('kernel');
        $templatesDirectory = $kernel->locateResource('@DamediaSpecialProjectBundle/Resources/views/Templates');

        foreach (scandir($templatesDirectory) as $fileName) {
            if ($fileName !== '.' AND $fileName !== '..') {
                $result[] = $fileName;
            }
        }

        return $result;
    }
}
?>
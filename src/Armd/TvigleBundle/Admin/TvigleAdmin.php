<?php

namespace Armd\TvigleBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class TvigleAdmin extends Admin
{
    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName, $container)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->container = $container;
    }
    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('city')
            ->add('location')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $video_files = $this->getVideoFileField();
        $formMapper
            ->with('General')
                ->add('title')
                ->add('tvigleId')
                ->add('description')
                ->add('city')
                ->add('location')
//                ->add('filename', 'choice', array('choices' => $video_files, 'property_path' => false))
                ->add('filename', 'choice', array('choices' => $video_files))
            ->end();
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
            ->add('city')
            ->add('location')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    public function getVideoFileField()
    {
        $videoPath = $this->container->get('armd_tvigle.configuration_pool')->getOption('video_directory');
        if(!$videoPath || !file_exists($videoPath) || !$handle = opendir($videoPath)) {
            throw new \Symfony\Component\HttpFoundation\File\Exception\FileException('Video path not found');
        }

        $res = array();
        while (false !== ($entry = readdir($handle))) {
            if($entry !== '.' && $entry !== '..') {
                $res[$entry] = $entry;
            }
        }

        return $res;
    }


}
<?php

namespace Armd\TvigleVideoBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class TvigleVideoAdmin extends Admin
{
    protected $translationDomain = 'ArmdTvigleVideoBundle';

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('created')
            ->add('description')
            ->add('tvigleId')
            ->add('code');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('tvigleId');

        $video = $this->getSubject();
        if ($video->getId()) {
            $formMapper->add('title')
                ->add('description');
        }
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('tvigleId')
            ->add('title')
            ->add('created');
    }


    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['updateMetadata'] = [
            'label' => 'Обновить метаданные Tvigle'
        ];

        return $actions;
    }



}
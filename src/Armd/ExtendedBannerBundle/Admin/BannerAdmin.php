<?php

namespace Armd\ExtendedBannerBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class BannerAdmin extends Admin
{
    protected $translationDomain = 'ArmdBannerBundle';

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('type')
            ->add('href')
            ->add('startDate')
            ->add('endDate')
            ->add('priority')
            ->add('viewCount')
            ->add('clickCount')
            ->add('maxViews');

        parent::configureShowField($showMapper);
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('type')
                ->add('name')
                ->add('href')
                ->add('openInNewWindow', null, array('required' => false))
                ->add('startDate')
                ->add('endDate')
                ->add('priority')
                ->add('maxViews')
            ->end()
            ->with('Media')
                ->add('image', 'sonata_type_model_list', array(), array('link_parameters'=>array('context'=>'banner')))
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
            ->addIdentifier('name')
            ->add('type')
            ->add('startDate')
            ->add('endDate')
            ->add('priority')
            ->add('viewCount')
            ->add('clickCount')
            ->add('maxViews')
        ;

        parent::configureListFields($listMapper);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('type');
    }

}
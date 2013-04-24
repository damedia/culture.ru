<?php

namespace Armd\PerfomanceBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class PerfomanceReviewAdmin extends Admin
{
    protected $translationDomain = 'ArmdPerfomanceBundle';

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('published')
            ->add('perfomance')
            ->add('author')
            ->add('createdAt')
            ->add('body');
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
                ->add('published')
                ->add('perfomance')
                ->add('author')
                ->add('createdAt')
                ->add('body')
            ->end()
        ;
    }


    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('published')
            ->add('perfomance')
            ->add('author');
    }


    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('published')
            ->add('perfomance')
            ->add('author')
            ->add('createdAt');
    }

    public function getBatchActions()
    {
        // retrieve the default (currently only the delete action) actions
        $actions = parent::getBatchActions();

        $actions['publish'] = array(
            'label' => $this->trans('state_publish', array(), $this->translationDomain),
            'ask_confirmation' => false
        );

        $actions['unpublish'] = array(
            'label' => $this->trans('state_unpublish', array(), $this->translationDomain),
            'ask_confirmation' => false
        );

        return $actions;
    }    

}

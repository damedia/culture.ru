<?php

namespace Armd\TheaterBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class BillboardAdmin extends Admin
{
    protected $datagridValues = array(
        '_sort_by'      => 'date',
        '_sort_order'   => 'DESC',
    );

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('theater')
            ->add('title')
            ->add('date')
        ;
        
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
                ->add('theater', null, array(
                    'required' => true,
                    'property' => 'title',
                    'label' => 'Theater',
                    'attr' => array('class' => 'chzn-select span5'),
                    'query_builder' => function($er) {
                        $qb = $er->createQueryBuilder('r');
                        $qb->orderBy('r.title', 'ASC');
                        return $qb;
                    }
                ))
                ->add('title')
                ->add('date')
            ->end();

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
            ->addIdentifier('title')
            ->add('date')
        ;
        
        parent::configureListFields($listMapper);        
    }
    
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('theater')
            ->add('title')
        ;
    }
}

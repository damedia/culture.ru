<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\MuseumBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

class Museum extends Admin
{
    protected $datagridValues = array(
        '_sort_by'      => 'title',    
        '_sort_order'   => 'ASC',
    );

    protected $translationDomain = 'ArmdMuseumBundle';

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('showOnMain')
            ->add('showOnMainOrd')
        ;
        
        parent::configureShowFields($showMapper);
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
                ->add('title')
                ->add('body')
                ->add('url')
                ->add('category')
                ->add('region', null, array(
                    'required' => false,
                    'property' => 'title',
                    'label' => 'Region',
                    'attr' => array('class' => 'chzn-select span5'),
                    'query_builder' => function($er) {
                        $qb = $er->createQueryBuilder('r');
                        $qb->orderBy('r.title', 'ASC');
                        return $qb;
                    }
                ))
                ->add('published', null, array('required' => false))
            ->end()
            ->with('Главная')
                ->add('showOnMain', null, array(
                    'required' => false
                ))
                ->add('showOnMainOrd', null, array(
                    'required' => false
                ))                
            ->end()
            ->with('Images of Russia')
                ->add('atlasObject', null, array(
                    'required' => false,
                    'property' => 'title',
                    'label' => 'Atlas Object',
                    'attr' => array('class' => 'chzn-select span5'),
                    'query_builder' => function($er) {
                        $qb = $er->createQueryBuilder('o');
                        $qb->orderBy('o.title', 'ASC')
                            ->where('o.showAtRussianImage = TRUE')
                            ->andWhere('o.published = TRUE')
                        ;
                        return $qb;
                    }
                ))
            ->end()
            ->with('Media')
                ->add('image', 'sonata_type_model_list', array(), array('link_parameters'=>array('context'=>'museum')))
                ->add('bannerImage', 'sonata_type_model_list', array(), array('link_parameters'=>array('context'=>'museum')))
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
            ->add('published')            
            ->add('showOnMain')
            ->add('showOnMainOrd')
        ;
        
        parent::configureListFields($listMapper);        
    }
    
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('published')
            ->add('title')
            ->add('showOnMain')
            ->add('showOnMainOrd')
        ;
    }    

    public function getBatchActions()
    {
        // retrieve the default (currently only the delete action) actions
        $actions = parent::getBatchActions();

        
        // check user permissions
        if($this->hasRoute('edit') && $this->isGranted('EDIT') && $this->hasRoute('delete') && $this->isGranted('DELETE')){
            // /*
            $actions['ShowOnMain']=array(
                'label'            => $this->trans('aShowOnMain', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
            );
            $actions['NotShowOnMain']=array(
                'label'            => $this->trans('aNotShowOnMain', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false // If true, a confirmation will be asked before performing the action
            );
            // */
        }
        
        return $actions;
    }    
}

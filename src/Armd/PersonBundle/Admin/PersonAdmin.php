<?php

namespace Armd\PersonBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\Admin;

class PersonAdmin extends Admin
{
    
    protected $container;

    public function __construct($code, $class, $baseControllerName, $serviceContainer)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $serviceContainer;
    }
        
    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')         
            ->add('description')
            ->add('lifeDates') 
            ->add('corrected')
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
                ->add('corrected', null, array('required' => false, 'disabled' => ($this->container->get('security.context')->isGranted('ROLE_CORRECTOR') ? false : true )))                   
                ->add('name')           
                ->add('description')
                ->add('lifeDates')
                ->add('personTypes', null, array(
                    'attr' => array('class' => 'chzn-select person-person-types-select')
                ))               
            ->end()
            ->with('Media')
                ->add('image', 'armd_media_file_type', array( 
                    'required' => false,
                    'with_remove' => true,
                    'media_context' => 'person',
                    'media_provider' => 'sonata.media.provider.image',
                    'media_format' => 'small',
                    'label' => 'Portrait'
                ))              
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
            ->add('corrected')       
        ;
        
        parent::configureListFields($listMapper);        
    }    
}

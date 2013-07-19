<?php

namespace Armd\MainBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Route\RouteCollection;

class ChangeHistoryAdmin extends Admin
{
    
    protected $datagridValues = array(
        '_sort_by'      => 'updatedAt',    
        '_sort_order'   => 'DESC',
    );
    
    protected $maxPerPage = 2500;
    protected $maxPageLinks = 0;    
        
    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('updatedAt')
            ->add('updatedBy')
            ->add('updatedIP')
        ;
        if (!$this->getRequest()->isXmlHttpRequest())
        {
            $showMapper -> add('entityClass');
            $showMapper -> add('entityId');
        }        
        $showMapper
            ->add('changes', null, array('template' => 'ArmdMainBundle:CRUD:show_changes_array.html.twig')); 
    }
        
    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        
        $listMapper
            ->add('updatedAt')
            ->add('updatedBy')
            ->add('updatedIP')
            ;
            
        if (!$this->getRequest()->isXmlHttpRequest())
        {
            $listMapper -> add('entityClass');
            $listMapper -> add('entityId');
        }
            
            
         $listMapper
            ->add('_action', 'actions', array('actions' => array(
                'view' => array()
            )));	            
    } 
    
   /**
    * @param \Sonata\AdminBundle\Route\RouteCollection $collection
    */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
        $collection->remove('delete');
        $collection->remove('edit');
    } 

   /**
    * @param string $context
    * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
    */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        
        if ($context == 'list') 
        {
            $alias = $query->getQueryBuilder()->getRootAlias();
            $entity_id = $this->getRequest()->get('entity_id');
            $entity_class = $this->getRequest()->get('entity_class');
            if ( $entity_id && $entity_class ) 
            {
                $query->getQueryBuilder()
                    ->andWhere("{$alias}.entityId = :entity_id")
                    ->setParameter('entity_id', $entity_id)
                    ->andWhere("{$alias}.entityClass = :entity_class")
                    ->setParameter('entity_class', $entity_class)
                    ;
            }	
        }


        return $query;
    }

    public function getTemplate($name)
    {
        switch ($name) {
            case 'ajax':
                return 'ArmdMainBundle:CRUD:change-history-ajax-layout.html.twig';
                break;   
            case 'list':
                return 'ArmdMainBundle:CRUD:change-history-list.html.twig';
                break;                                
            default:
                return parent::getTemplate($name);
                break;
        }
    }    
       
}
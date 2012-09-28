<?php
namespace Armd\MkCommentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CommentAdmin extends Admin
{
    protected $translationDomain = 'ArmdMkCommentBundle';

    protected $datagridValues = array(
        '_sort_order' => 'DESC', // Descendant ordering (default = 'ASC')
        '_sort_by' => 'createdAt' // name of the ordered field (default = the model id field, if any)
    );

    public function createQuery($context = 'list')
    {
        $qb = $this->modelManager->getEntityManager('ArmdMkCommentBundle:Comment')
            ->createQueryBuilder()
            ->select('c')
            ->from('ArmdMkCommentBundle:Comment', 'c');

        return new ProxyQuery($qb);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('edit')
            ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('thread.id')
            ->add('stateString', null, array('template' => 'ArmdMkCommentBundle:Admin:list_state.html.twig'))
            ->add('createdAt')
            ->add('author')
            ->add('body')
            ->add('moderatedBy')
            ->add('moderatedAt')

        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper;
    }


    public function getBatchActions()
    {
        // retrieve the default (currently only the delete action) actions
        $actions = parent::getBatchActions();
//        $actions = array();

        $actions['statePending'] = array(
            'label' => $this->trans('state_pending', array(), $this->translationDomain),
            'ask_confirmation' => false
        );

//        $actions['stateDeleted'] = array(
//            'label' => $this->trans('state_deleted', array(), $this->translationDomain),
//            'ask_confirmation' => false
//        );
//
//        $actions['stateSpam'] = array(
//            'label' => $this->trans('state_spam', array(), $this->translationDomain),
//            'ask_confirmation' => false
//        );

        $actions['stateVisible'] = array(
            'label' => $this->trans('state_visible', array(), $this->translationDomain),
            'ask_confirmation' => false
        );


        return $actions;
    }

}
<?php

namespace Armd\SubscriptionBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Route\RouteCollection;

use Sonata\AdminBundle\Admin\Admin;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class MailingListAdmin extends Admin
{
    protected $container;

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            //->remove('create')
            ->remove('delete')
            ->add('toggleEnabled', $this->getRouterIdParameter().'/toggleEnabled');
    }

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
            ->add('description')
            ->add('issueSignature')
            ->add('periodically')
            ->add('enabled', null, array('label' => $this->trans('subscriptions.enabled')))
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
                ->add('title')
                ->add('description')
                ->add('issueSignature')
                ->add('periodically')
                ->add('enabled', null, array('label' => $this->trans('subscriptions.enabled')))
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
            ->addIdentifier('title')
            ->add('description')
            ->add('issueSignature')
            ->add('periodically')
            ->add('enabled', null, array('label' => $this->trans('subscriptions.enabled')))
            ->add('_action', 'actions', array(
                    'actions' => array( 
                        'toggleEnabled' => array('template' => 'ArmdSubscriptionBundle:Admin:mailinglist_enable.html.twig'),
                    )
                ))
        ;

        parent::configureListFields($listMapper);
    }
}

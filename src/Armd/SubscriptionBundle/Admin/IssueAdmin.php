<?php

namespace Armd\SubscriptionBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

use Sonata\AdminBundle\Admin\Admin;

use Doctrine\ORM\EntityRepository;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class IssueAdmin extends Admin
{
    protected $container;

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
            ->add('mailingList')
            ->add('createdAt')
            ->add('sendedAt')
            ->add('content')
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
                ->add('mailingList', null, array(
                    'query_builder' => function(EntityRepository $repository) {
                        return $repository->createQueryBuilder('ml')
                            ->where('ml.periodically = false');
                    }
                ))
                ->add('content')
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
            ->addIdentifier('id')
            ->add('mailingList')
            ->add('createdAt')
            ->add('sendedAt')
        ;

        parent::configureListFields($listMapper);
    }
}

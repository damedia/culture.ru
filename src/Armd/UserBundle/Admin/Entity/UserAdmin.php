<?php

namespace Armd\UserBundle\Admin\Entity;

use Sonata\UserBundle\Admin\Entity\UserAdmin as BaseAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;

class UserAdmin extends BaseAdmin {

    protected $translationDomain = 'ArmdUserBundle';

    protected function configureListFields(ListMapper $listMapper)
    {
        parent::configureListFields($listMapper);

        $listMapper->add('actions', 'text', array('template' => 'ArmdUserBundle:Admin:user_row_actions.html.twig'));
    }

    public function getListTemplate()
    {
        return 'ArmdUserBundle:Admin:list.html.twig';
    }

}


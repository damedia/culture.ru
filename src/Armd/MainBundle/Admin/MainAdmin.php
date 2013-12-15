<?php

namespace Armd\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Main administer
 */
class MainAdmin extends Admin
{
    protected $baseRouteName = 'main';
    protected $baseRoutePattern = 'main';
    protected $classnameLabel = 'main';

    /**
     * Get administer icon
     *
     * @return string
     */
    public function getIcon()
    {
        return '.png';
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(array('list'));
    }

}
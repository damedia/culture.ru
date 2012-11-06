<?php

namespace Armd\MainBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Armd\MainBundle\Doctrine\DBAL\MasterSlaveConnection;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;

class MasterSlaveListener {

    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function onKernelRequest(KernelEvent $event)
    {
        if ($this->connection instanceof MasterSlaveConnection) {
            $route = $event->getRequest()->get('_route');
            if (strpos($route, 'admin_') === 0) {
                $this->connection->connect('master');
            }
        }
    }

}
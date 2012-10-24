<?php
namespace Armd\UtilBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Doctrine\DBAL\Connections\MasterSlaveConnection;
use Symfony\Component\DependencyInjection\Container;

class LocaleDatabaseSwitchListener {
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        \gFuncs::dbgWriteLogVar($event->getRequest()->getLocale(), false, 'locale'); // DBG:
        if($event->getRequest()->getLocale() === 'en')
        {
            $connection = $this->container->get('doctrine.dbal.default_connection');
            $connection->close(); // for sure ;)

            if ($connection instanceof MasterSlaveConnection) {
                $refConn = new \ReflectionObject($connection);
                $refConnections = $refConn->getProperty('connections');
//                $refConnections->setAccessible('public'); //we have to change it for a moment

//                $connections = $refConnections->getValue($connection);


//                $refConnections->setAccessible('protected');
//                $refConnections->setValue($connection, $connections);

            } else {
                $refConn = new \ReflectionObject($connection);
                $refParams = $refConn->getProperty('_params');
                $refParams->setAccessible('public'); //we have to change it for a moment

                $params = $refParams->getValue($connection);
                $params['dbname'] = $this->container->getParameter('database_name_en');

                $refParams->setAccessible('private');
                $refParams->setValue($connection, $params);
            }

            $this->container->get('doctrine')->resetEntityManager('default'); // for sure (unless you like broken transactions)


        }
    }
}
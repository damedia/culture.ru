<?php

namespace Armd\MainBundle\Doctrine\DBAL;

use Doctrine\DBAL\Connections\MasterSlaveConnection as BaseConnection;
use Doctrine\DBAL\Cache\QueryCacheProfile;

class MasterSlaveConnection extends BaseConnection
{
    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null)
    {
        if (strpos(strtolower($query), 'nextval') !== false) {
            $this->connect('master');
        }

        return parent::executeQuery($query, $params, $types, $qcp);
    }
}
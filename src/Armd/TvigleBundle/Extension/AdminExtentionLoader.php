<?php

namespace Armd\Bundle\CmsBundle\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AdminExtentionLoader
{
    protected $container = null;

    protected $extServiceIds = array();

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container  = $container;
    }

    /**
     * @param array $extServiceIds
     * @return void
     */
    public function setExtentionServiceIds(array $extServiceIds)
    {
        $this->extServiceIds = $extServiceIds;
    }

    /**
     * @return array
     */
    public function getExtentionServiceIds()
    {
        return $this->extServiceIds;
    }
}
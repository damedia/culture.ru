<?php

namespace Armd\TvigleBundle\Pool;

#use Symfony\Component\DependencyInjection\ContainerInterface;
#use Symfony\Component\DependencyInjection\Reference;


class ConfigurationPool
{

    protected $options = array();

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * @param $name
     * @param $value
     * @return void
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }
}
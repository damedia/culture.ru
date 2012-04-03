<?php

namespace Armd\CultureAreaBundle\Tests\Controller;

use Armd\Bundle\CmsBundle\UsageType\UsageType;

use PHPUnit_Framework_TestCase;

class CultureAreaControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function testIndexAction()
    {
        $this->setContainer();

        $controller = $this->getMockBuilder('Armd\\CultureAreaBundle\\Controller\\TestController')
             ->setMethods(array('renderCms', 'setContainer'))
             ->disableOriginalConstructor()
             ->getMock();

        $controller->expects($this->any())->method('renderCms')
            ->with($this->equalTo(new UsageType()),
                   $this->equalTo(array('message' => 'Simple bundle')))
            ->will($this->returnValue(true));

        $controller->setContainer($this->container);
        $controller->indexAction(new UsageType(), 'Simple bundle');
    }

    public function setContainer()
    {
        $this->container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

    }
}

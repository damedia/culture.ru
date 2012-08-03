<?php
namespace Armd\AtlasBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

abstract class AbstractFunctionalTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;
    protected $container;
    protected $router;
    protected $application;
    protected $mediaDir;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->application = new Application($kernel);
        $this->container = $kernel->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->router = $this->container->get('router');

        $this->mediaDir = $this->container->get('kernel')->getRootDir() . '/../web/uploads/media';

    }

    public function loadFixtures()
    {
        $this->emptyDir($this->mediaDir);
        $this->runConsole('doctrine:fixtures:load', array('--fixtures' => __DIR__ . '/../DataFixtures/ORM'));
    }

    protected function runConsole($command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        $input = new ArrayInput($options);
        $output = new ConsoleOutput();

        $this->application->setAutoExit(false);
        $this->application->run($input, $output);
    }

    function emptyDir($dir, $DeleteMe = false)
    {
        if (!$dh = @opendir($dir)) return;
        while (false !== ($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..') continue;
            if (!@unlink($dir . '/' . $obj)) $this->emptyDir($dir . '/' . $obj, true);
        }

        closedir($dh);
        if ($DeleteMe) {
            @rmdir($dir);
        }
    }

    public function compareFiles($path1, $path2)
    {
        $crc1 = crc32(file_get_contents($path1));
        $crc2 = crc32(file_get_contents($path2));

        return $crc1 === $crc2;
    }

    public function countFiles($dir)
    {
        $paths = glob($dir . '/*');
        return count($paths);
    }
}
<?php
namespace Armd\AtlasBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Armd\TvigleBundle\Entity\Tvigle;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;


class LoadTestTvigleData extends AbstractFixture implements OrderedFixtureInterface
{
    private $manager;
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadReadyTvigles();
    }

    function loadReadyTvigles()
    {

        $tvigle = new Tvigle();
        $tvigle->setDescription('Видео 1 Tvigle');
        $tvigle->setTitle('Видео 1 title');
        $tvigle->setTvigleId('667577');
        $tvigle->setCode('<object id="va5b28f018304c93650d4e1bb3a233d33" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="720" height="405" align="middle"><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="movie" value="http://photo.tvigle.ru/resource/rf/swf/a5/b2/8f/018304c93650d4e1bb3a233d33.swf"></param><embed src="http://photo.tvigle.ru/resource/rf/swf/a5/b2/8f/018304c93650d4e1bb3a233d33.swf" width="720" height="405"  allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>');
        $tvigle->setDuration(150);
        $tvigle->setImage('/armdatlasbundle/images/test/videoimage/videoimage1.jpg');
        $this->manager->persist($tvigle);
        $this->addReference('armd.atlas.tvigle.video1', $tvigle);

        $tvigle = new Tvigle();
        $tvigle->setDescription('Видео 2 Tvigle description');
        $tvigle->setTitle('Видео 2 title');
        $tvigle->setTvigleId('666590');
        $tvigle->setCode('<object id="v3decda7645261ae7096979025399eada" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="720" height="540" align="middle"><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="movie" value="http://photo.tvigle.ru/resource/rf/swf/3d/ec/da/7645261ae7096979025399eada.swf"></param><embed src="http://photo.tvigle.ru/resource/rf/swf/3d/ec/da/7645261ae7096979025399eada.swf" width="720" height="540"  allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>');
        $tvigle->setDuration(150);
        $tvigle->setImage('/armdatlasbundle/images/test/videoimage/videoimage1.jpg');
        $this->manager->persist($tvigle);
        $this->addReference('armd.atlas.tvigle.video2', $tvigle);

        $tvigle = new Tvigle();
        $tvigle->setDescription('Видео 3 Tvigle');
        $tvigle->setTitle('Видео 3 title');
        $tvigle->setTvigleId('524568');
        $tvigle->setCode('<object id="vba8131b15a49ba93c68f11b981f26ab9" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="720" height="405" align="middle"><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><param name="movie" value="http://photo.tvigle.ru/resource/rf/swf/ba/81/31/b15a49ba93c68f11b981f26ab9.swf"></param><embed src="http://photo.tvigle.ru/resource/rf/swf/ba/81/31/b15a49ba93c68f11b981f26ab9.swf" width="720" height="405"  allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></object>');
        $tvigle->setDuration(150);
        $tvigle->setImage('/armdatlasbundle/images/test/videoimage/videoimage1.jpg');
        $this->manager->persist($tvigle);
        $this->addReference('armd.atlas.tvigle.video3', $tvigle);

        $this->manager->flush();

    }


//    function runTvigleCommand()
//    {
//        $command = $this->getApplication()->find('armd:tvigle:taskProcess');
//
//        $arguments = array(
//            'command' => 'armd:tvigle:taskProcess',
//            'name'    => 'Fabien',
//            '--yell'  => true,
//        );
//
//        $input = new ArrayInput($arguments);
//        $output = new \Symfony\Component\Console\Output\ConsoleOutput();
//        $returnCode = $command->run($input, $output);
//
//        return $returnCode;
//    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 10;
    }
}
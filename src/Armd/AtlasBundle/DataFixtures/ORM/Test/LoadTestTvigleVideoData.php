<?php
namespace Armd\AtlasBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Armd\TvigleVideoBundle\Entity\TvigleVideo;
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

        $tvigle = new TvigleVideo();
        $tvigle->setDescription('Видео 1 Tvigle');
        $tvigle->setTitle('Видео 1 title');
        $tvigle->setTvigleId('1860195');
        $tvigle->setCode('<object id="v1860195_1"  classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="442" height="249" align="middle">
        <param name="allowFullScreen" value="true" />
        <param name="movie" value="http://pub.tvigle.ru/swf/tvigle_single_v2.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#000000" />
        <param name="flashvars" value="prt=85deec0537d438363b1c3b54dbe222e8&id=1860195&srv=pub.tvigle.ru&w=442&h=249&dopparam=armada_skin&modes=1&nl=1" />
        <param name="allowscriptaccess" value="always" />
        <embed src="http://pub.tvigle.ru/swf/tvigle_single_v2.swf"
        quality="high" width="442" height="249" bgcolor="#000000"  allowscriptaccess="always"
        allowfullscreen="true" flashvars="prt=85deec0537d438363b1c3b54dbe222e8&id=1860195&srv=pub.tvigle.ru&w=442&h=249&dopparam=armada_skin&modes=1&nl=1"
        type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
        </object>
        ');
        $tvigle->setDuration(4960);
        $tvigle->setImage('http://photo.tvigle.ru/res/prt/85deec0537d438363b1c3b54dbe222e8/01/95/000001860195/pub.jpg');
        $this->manager->persist($tvigle);
        $this->addReference('armd.atlas.tvigle.video1', $tvigle);

        $tvigle = new TvigleVideo();
        $tvigle->setDescription('Видео 2 Tvigle description');
        $tvigle->setTitle('Видео 2 title');
        $tvigle->setTvigleId('1860195');
        $tvigle->setCode('<object id="v1860195_1"  classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="442" height="249" align="middle">
        <param name="allowFullScreen" value="true" />
        <param name="movie" value="http://pub.tvigle.ru/swf/tvigle_single_v2.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#000000" />
        <param name="flashvars" value="prt=85deec0537d438363b1c3b54dbe222e8&id=1860195&srv=pub.tvigle.ru&w=442&h=249&dopparam=armada_skin&modes=1&nl=1" />
        <param name="allowscriptaccess" value="always" />
        <embed src="http://pub.tvigle.ru/swf/tvigle_single_v2.swf"
        quality="high" width="442" height="249" bgcolor="#000000"  allowscriptaccess="always"
        allowfullscreen="true" flashvars="prt=85deec0537d438363b1c3b54dbe222e8&id=1860195&srv=pub.tvigle.ru&w=442&h=249&dopparam=armada_skin&modes=1&nl=1"
        type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
        </object>
        ');
        $tvigle->setDuration(4960);
        $tvigle->setImage('http://photo.tvigle.ru/res/prt/85deec0537d438363b1c3b54dbe222e8/01/95/000001860195/pub.jpg');
        $this->manager->persist($tvigle);
        $this->addReference('armd.atlas.tvigle.video2', $tvigle);

        $tvigle = new TvigleVideo();
        $tvigle->setDescription('Видео 3 Tvigle');
        $tvigle->setTitle('Видео 3 title');
        $tvigle->setTvigleId('1860195');
        $tvigle->setCode('<object id="v1860195_1"  classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="442" height="249" align="middle">
        <param name="allowFullScreen" value="true" />
        <param name="movie" value="http://pub.tvigle.ru/swf/tvigle_single_v2.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#000000" />
        <param name="flashvars" value="prt=85deec0537d438363b1c3b54dbe222e8&id=1860195&srv=pub.tvigle.ru&w=442&h=249&dopparam=armada_skin&modes=1&nl=1" />
        <param name="allowscriptaccess" value="always" />
        <embed src="http://pub.tvigle.ru/swf/tvigle_single_v2.swf"
        quality="high" width="442" height="249" bgcolor="#000000"  allowscriptaccess="always"
        allowfullscreen="true" flashvars="prt=85deec0537d438363b1c3b54dbe222e8&id=1860195&srv=pub.tvigle.ru&w=442&h=249&dopparam=armada_skin&modes=1&nl=1"
        type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
        </object>
        ');
        $tvigle->setDuration(4960);
        $tvigle->setImage('http://photo.tvigle.ru/res/prt/85deec0537d438363b1c3b54dbe222e8/01/95/000001860195/pub.jpg');
        $this->manager->persist($tvigle);
        $this->addReference('armd.atlas.tvigle.video3', $tvigle);

        $this->manager->flush();

    }


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
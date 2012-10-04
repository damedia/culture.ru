<?php
namespace Armd\AtlasBundle\DataFixtures\ORM\Init;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Yaml\Parser;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Armd\AtlasBundle\Entity\Region;

class LoadRegionData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/regions.yml'));
        foreach($data['regions'] as $regionData) {
            $region = new Region();
            $region->setTitle($regionData['title']);
            if(!empty($regionData['sortIndex'])) {
                $region->setSortIndex($regionData['sortIndex']);
            }
            if(!empty($regionData['ref_code'])) {
                $this->addReference($regionData['ref_code'], $region);
            }
            $manager->persist($region);
        }

        $manager->flush();
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
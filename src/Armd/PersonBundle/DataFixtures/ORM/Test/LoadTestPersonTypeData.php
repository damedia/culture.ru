<?php
namespace Armd\PersonBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Armd\PersonBundle\Entity\PersonType;
use Symfony\Component\Yaml\Parser;

class LoadTestPersonTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_person_types.yml'));
        
        foreach ($data['personTypes'] as $d) {
            $personType = new PersonType();
            $personType->setTitle($d['title']);
            $personType->setSlug($d['slug']);

            if (!empty($d['ref_code'])) {
                $this->addReference($d['ref_code'], $personType);
            }

            $manager->persist($personType);
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
        return 100;
    }
}
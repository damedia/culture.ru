<?php
namespace Armd\PersonBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Armd\PersonBundle\Entity\Person;
use Symfony\Component\Yaml\Parser;

class LoadTestPersonData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_person.yml'));
        
        foreach ($data['person'] as $d) {
            $person = new Person();
            $person->setName($d['name']);
            $person->setDescription($d['description']);
            
            if (!empty($d['personTypes'])) {
                foreach ($d['personTypes'] as $ref) {
                    $personType = $this->getReference($ref);
                    
                    if (empty($personType)) {
                        throw new \InvalidArgumentException('PersonType reference ' . $ref . ' not found');
                    }
                    
                    $person->addPersonType($personType);
                }
            }

            if (!empty($d['ref_code'])) {
                $this->addReference($d['ref_code'], $person);
            }

            $manager->persist($person);
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
        return 101;
    }
}
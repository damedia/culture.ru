<?php
namespace Armd\MuseumBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Armd\MuseumBundle\Entity\Category;
use Symfony\Component\Yaml\Parser;


class LoadTestCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {       
        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_categories.yml'));

        foreach ($data['categories'] as $categoryData) {
            $category = new Category();
            $category->setTitle($categoryData['title']);  

            if (!empty($categoryData['ref_code'])) {
                $this->addReference($categoryData['ref_code'], $category);
            }

            $manager->persist($category);
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
<?php
namespace Armd\ExhibitBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Armd\ExhibitBundle\Entity\Category;
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
        $rootCategory = $manager->getRepository('Armd\ExhibitBundle\Entity\Category')->findOneBy(array('lvl' => 0));
        
        if (!$rootCategory) {
            $rootCategory = new Category();
            $rootCategory->setTitle('== Корневая категория ==');
            $rootCategory->setPublished(true);
            $manager->persist($rootCategory);
        }

        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_categories.yml'));

        foreach ($data['categories'] as $categoryData) {
            $this->saveCategory($manager, $categoryData, $rootCategory);
        }
        $manager->flush();
    }

    function saveCategory(ObjectManager $manager, array $categoryData, Category $parentCategory)
    {
        $category = new Category();
        $category->setParent($parentCategory);
        $category->setTitle($categoryData['title']);  
        $category->setPublished(true);
        
        if (!empty($categoryData['ref_code'])) {
            $this->addReference($categoryData['ref_code'], $category);
        }
        
        $manager->persist($category);

        if (!empty($categoryData['children'])) {
            foreach ($categoryData['children'] as $childrenCategoryData) {
                $this->saveCategory($manager, $childrenCategoryData, $category);
            }
        }

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
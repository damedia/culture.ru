<?php
namespace Armd\LectureBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Armd\LectureBundle\Entity\LectureCategory as Category;
use Symfony\Component\Yaml\Parser;


class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
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

        foreach ($data['categories'] as $rootKey => $rootVal) {
            $rootCategory = $this->getReference('armd_lecture.lecture_category.' . $rootKey);
            foreach($rootVal as $categoryData) {
                $this->saveCategory($manager, $categoryData, $rootCategory);
            }
        }
        $manager->flush();
    }

    function saveCategory(ObjectManager $manager, array $categoryData, Category $parentCategory)
    {
        $category = new Category();
        $category->setParent($parentCategory);
        $category->setTitle($categoryData['title']);
        if (!empty($categoryData['ref_code'])) {
            $this->addReference('armd_lecture.lecture_category.' . $categoryData['ref_code'], $category);
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
        return 15;
    }
}
<?php
namespace Armd\LectureBundle\DataFixtures\ORM\Init;

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
        // create root lecture category
        $rootCategory = new Category();
        $rootCategory->setTitle('== Корневая категория (Лекции) ==');
        $rootCategory->setLectureSuperType($this->getReference('armd_lecture.lecture_super_type.lecture_super_type_lecture'));
        $manager->persist($rootCategory);
        $this->addReference('armd_lecture.lecture_category.lecture_root', $rootCategory);

        // create root tranlation category
        $rootCategory = new Category();
        $rootCategory->setTitle('== Корневая категория (Трансляции) ==');
        $rootCategory->setLectureSuperType($this->getReference('armd_lecture.lecture_super_type.lecture_super_type_video_translation'));
        $manager->persist($rootCategory);
        $this->addReference('armd_lecture.lecture_category.translation_root', $rootCategory);
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
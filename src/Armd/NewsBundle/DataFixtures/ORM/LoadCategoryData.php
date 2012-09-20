<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Armd\NewsBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setTitle('Категория новостей 1');
        $category->setFiltrable(true);
        $category->setPriority('10');
        $category->setSlug('categoryone');
        $manager->persist($category);
        $this->addReference('armd_news.category.category1', $category);

        $category = new Category();
        $category->setTitle('Категория новостей 2');
        $category->setFiltrable(false);
        $category->setPriority('20');
        $category->setSlug('categorytwo');
        $manager->persist($category);
        $this->addReference('armd_news.category.category2', $category);

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
<?php
namespace Armd\AtlasBundle\DataFixtures\ORM\Init;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Armd\AtlasBundle\Entity\WeekDay;

class LoadInitWeekDayData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $days = array('пн', 'вт', 'ср', 'чт' , 'пт', 'сб', 'вс');
        $sortIndex = 1;
        foreach($days as $dayData)
        {
            $day = new WeekDay();
            $day->setName($dayData);
            $day->setSortIndex($sortIndex);
            $manager->persist($day);
            $this->addReference('armd_atlas.weekday.day' . $sortIndex,  $day);

            $sortIndex++;
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
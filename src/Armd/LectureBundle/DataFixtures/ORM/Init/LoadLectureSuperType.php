<?php
namespace Armd\LectureBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Armd\LectureBundle\Entity\LectureSuperType;
use Symfony\Component\Yaml\Parser;


class LoadLectureSuperTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $types = array(
          'LECTURE_SUPER_TYPE_LECTURE' => 'Лекция',
          'LECTURE_SUPER_TYPE_VIDEO_TRANSLATION' => 'Трансляция',
        );

        foreach($types as $key => $val) {
            $type = new LectureSuperType();
            $type->setCode($key);
            $type->setName($val);
            $manager->persist($type);
            $this->setReference('armd_lecture.lecture_super_type.' . strtolower($key), $type);
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
        return 5;
    }
}

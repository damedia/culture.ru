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
        $type = new LectureSuperType();
        $type->setCode('LECTURE_SUPER_TYPE_LECTURE');
        $type->setName('Лекция');
        $manager->persist($type);
        $this->setReference('armd_lecture.lecture_super_type.lecture', $type);

        $type = new LectureSuperType();
        $type->setCode('LECTURE_SUPER_TYPE_VIDEO_TRANSLATION');
        $type->setName('Трансляция');
        $manager->persist($type);
        $this->setReference('armd_lecture.lecture_super_type.translation', $type);

        $type = new LectureSuperType();
        $type->setCode('LECTURE_SUPER_TYPE_CINEMA');
        $type->setName('Кино');
        $manager->persist($type);
        $this->setReference('armd_lecture.lecture_super_type.cinema', $type);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 210;
    }
}

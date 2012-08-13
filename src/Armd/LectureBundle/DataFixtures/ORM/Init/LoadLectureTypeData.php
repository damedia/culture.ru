<?php
namespace Armd\LectureBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Armd\LectureBundle\Entity\LectureType;
use Symfony\Component\Yaml\Parser;


class LoadLectureTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $types = array(
          'LECTURE_TYPE_VIDEO' => 'Видео',
          'LECTURE_TYPE_AUDIO' => 'Аудио',
          'LECTURE_TYPE_TEXT' => 'Текст',
        );

        foreach($types as $key => $val) {
            $type = new LectureType();
            $type->setCode($key);
            $type->setName($val);
            $manager->persist($type);
            $this->setReference('armd_lecture.lecture_type.' . strtolower($key), $type);
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

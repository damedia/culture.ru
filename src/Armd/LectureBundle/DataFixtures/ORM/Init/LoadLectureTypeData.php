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
        $type = new LectureType();
        $type->setCode('LECTURE_TYPE_VIDEO');
        $type->setName('Видео');
        $manager->persist($type);
        $this->setReference('armd_lecture.lecture_type.video', $type);

        $type = new LectureType();
        $type->setCode('LECTURE_TYPE_AUDIO');
        $type->setName('Аудио');
        $manager->persist($type);
        $this->setReference('armd_lecture.lecture_type.audio', $type);


        $type = new LectureType();
        $type->setCode('LECTURE_TYPE_TEXT');
        $type->setName('Текст');
        $manager->persist($type);
        $this->setReference('armd_lecture.lecture_type.text', $type);

        $type = new LectureType();
        $type->setCode('LECTURE_TYPE_URL');
        $type->setName('Ссылка');
        $manager->persist($type);
        $this->setReference('armd_lecture.lecture_type.url', $type);

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

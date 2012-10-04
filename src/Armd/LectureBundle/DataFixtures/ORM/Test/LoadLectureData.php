<?php
namespace Armd\LectureBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Yaml\Parser;
use Application\Sonata\MediaBundle\Entity\Media;
use Armd\LectureBundle\Entity\Lecture;


class LoadLectureData extends AbstractFixture implements OrderedFixtureInterface
{

    private $om;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $this->om = $manager;

        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_lectures.yml'));

        foreach ($data['lectures'] as $objectData) {
            $lecture = $this->createLecture($objectData);
            $manager->persist($lecture);
        }
        $manager->flush();
    }

    function createLecture(array $data)
    {
        $lecture = new Lecture();
        $simpleFields = array(
            'title',
            'lecturer',
            'recommended',
        );

        foreach ($simpleFields as $simpleField) {
            if (isset($data[$simpleField])) {
                $methodName = 'set' . ucfirst($simpleField);
                if (method_exists($lecture, $methodName)) {
                    $lecture->$methodName($data[$simpleField]);
                }
            }
        }

        if(!empty($data['categories'])) {
            foreach($data['categories'] as $categoryRef) {
                $category = $this->getReference($categoryRef);
                if(empty($category)) {
                    throw new \InvalidArgumentException('Category reference ' . $categoryRef . ' not found');
                }
                $lecture->addCategory($category);
                $this->om->persist($category);
            }
        }

        if(!empty($data['lectureSuperType'])) {
            $lectureSuperType = $this->getReference($data['lectureSuperType']);
            if(empty($lectureSuperType)) {
                throw new \InvalidArgumentException('LectureSuperType reference ' . $data['lectureSuperType'] . ' not found');
            }
            $lecture->setLectureSuperType($lectureSuperType);

        }

        if(!empty($data['lectureType'])) {
            $lectureType = $this->getReference($data['lectureType']);
            if(empty($lectureType)) {
                throw new \InvalidArgumentException('Lecture type reference ' . $data['lectureType'] . ' not found');
            }
            $lecture->setLectureType($lectureType);
        }

        if(!empty($data['lectureFile'])) {
            $lecture->setLectureFile($this->createMedia($data['lectureFile']));
        }

        if(!empty($data['lectureVideo'])) {

            $video = $this->getReference($data['lectureVideo']);
            if(empty($video)) {
                throw new \InvalidArgumentException('Category reference ' . $data['lectureVideo'] . ' not found');
            }
            $lecture->setLectureVideo($video);
        }

        return $lecture;
    }

    public static function createMedia($fileName) {
        $file = self::createUploadedFile($fileName);
        $media = new Media();
        $media->setBinaryContent($file);
        $media->setContext(static::getContext());
        $media->setProviderName('sonata.media.provider.file');
        return $media;
    }

    public static function createUploadedFile($fileName)
    {
        $filePath = __DIR__ . '/../../../Resources/fixtures/lectures/' . $fileName;
        if(!file_exists($filePath)) {
            throw new \InvalidArgumentException('File file not found ' . $fileName);
        }

        $file = new UploadedFile($filePath, $fileName);
        return $file;
    }


    public static function getContext() {
        return 'lecture';
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 230;
    }
}
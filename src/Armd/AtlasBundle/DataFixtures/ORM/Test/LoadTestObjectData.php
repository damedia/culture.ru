<?php
namespace Armd\AtlasBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Application\Sonata\MediaBundle\Entity\GalleryHasMedia;
use Application\Sonata\MediaBundle\Entity\Gallery;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Armd\AtlasBundle\Entity\Category;
use Symfony\Component\Yaml\Parser;
use Armd\AtlasBundle\Entity\Object;
use Application\Sonata\MediaBundle\Entity\Media;


class LoadTestObjectData extends AbstractFixture implements OrderedFixtureInterface
{

    private $om;
    private $galleryNum = 1;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $this->om = $manager;

        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_objects.yml'));

        foreach ($data['objects'] as $objectData) {
            $object = $this->createObject($objectData);
            $manager->persist($object);
        }
        $manager->flush();
    }

    function createObject(array $data)
    {
        $object = new Object();
        $simpleFields = array(
            'title',
            'announce',
            'content',
            'siteUrl',
            'email',
            'phone',
            'address',
            'lat',
            'lon',
            'workTime',
            'virtualTour',
            'showAtRussianImage'
        );

        foreach ($simpleFields as $simpleField) {
            if (isset($data[$simpleField])) {
                $methodName = 'set' . ucfirst($simpleField);
                if (method_exists($object, $methodName)) {
                    $object->$methodName($data[$simpleField]);
                }
            }
        }

        if(!empty($data['primaryImage'])) {
            $object->setPrimaryImage($this->createMediaImage($data['primaryImage']));
        }


        if(!empty($data['images'])) {
            foreach($data['images'] as $fileName) {
                $object->addImage($this->createMediaImage($fileName));
            }
        }

        if(!empty($data['archiveImages'])) {
            foreach($data['archiveImages'] as $fileName) {
                $object->addArchiveImage($this->createMediaImage($fileName));
            }
        }

        if(!empty($data['image3d'])) {
            $object->setImage3d($this->createMediaImage($data['image3d']));
        }

        if(!empty($data['videos'])) {
            foreach($data['videos'] as $videoRef) {
                $video = $this->getReference($videoRef);
                if(empty($video)) {
                    throw new \InvalidArgumentException('Category reference ' . $videoRef . ' not found');
                }
                $object->addVideo($video);
            }
        }

        if(!empty($data['primaryCategory'])) {
            $category = $this->getReference($data['primaryCategory']);
            if(empty($category)) {
                throw new \InvalidArgumentException('Category reference ' . $data['primaryCategory'] . ' not found');
            }
            $object->setPrimaryCategory($category);
        }

        if(!empty($data['secondaryCategories'])) {
            foreach($data['secondaryCategories'] as $categoryRef) {
                $category = $this->getReference($categoryRef);
                if(empty($category)) {
                    throw new \InvalidArgumentException('Category reference ' . $categoryRef . ' not found');
                }
                $object->addSecondaryCategory($category);
            }
        }

        if(!empty($data['regions'])) {
            foreach($data['regions'] as $regionRef) {
                $region = $this->getReference($regionRef);
                if(!empty($region)) {
                    $object->addRegion($region);
                }
            }
        }

        if (!empty($data['createdBy'])) {
            $object->setCreatedBy($this->getReference($data['createdBy']));
        }

        if (!empty($data['ref_code'])) {
            $this->addReference($data['ref_code'], $object);
        }

        return $object;
    }

    public static function createMediaImage($fileName) {
        $file = self::createUploadedFile($fileName);
        $media = new Media();
        $media->setBinaryContent($file);
        $media->setContext(static::getContext());
        $media->setProviderName('sonata.media.provider.image');
        return $media;
    }

    public static function createUploadedFile($fileName)
    {
        $filePath = __DIR__ . '/../../../Resources/fixtures/images/' . $fileName;
        if(!file_exists($filePath)) {
            throw new \InvalidArgumentException('Media file not found ' . $fileName);
        }

        $file = new UploadedFile($filePath, $fileName);
        return $file;
    }


    public static function getContext() {
        return 'atlas';
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 130;
    }
}
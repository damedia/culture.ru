<?php
namespace Armd\MuseumBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Yaml\Parser;
use Armd\MuseumBundle\Entity\RealMuseum;
use Application\Sonata\MediaBundle\Entity\Media;


class LoadTestArtObjectData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {
        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_real_museums.yml'));

        foreach ($data['realMuseums'] as $objectData) {
            $object = $this->createObject($objectData);
            $manager->persist($object);
        }
        
        $manager->flush();
    }

    function createObject(array $data)
    {
        $object = new RealMuseum();      
        $object->setTitle($data['title']);
        $object->setDescription($data['description']);
        $object->setAddress($data['address']);
        $object->setUrl($data['url']);
        
        if(!empty($data['category'])) {
            $category = $this->getReference($data['category']);

            if (empty($category)) {
                throw new \InvalidArgumentException('Category reference ' . $data['category'] . ' not found');
            }

            $object->setCategory($category);
        }
        
        if(!empty($data['image'])) {
            $object->setImage($this->createMediaImage($data['image']));
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


    public static function getContext() 
    {
        return 'museum';
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 101;
    }
}
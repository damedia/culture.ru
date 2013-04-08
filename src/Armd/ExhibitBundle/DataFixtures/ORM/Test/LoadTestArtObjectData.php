<?php
namespace Armd\ExhibitBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\Yaml\Parser;
use Armd\ExhibitBundle\Entity\ArtObject;
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
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_art_objects.yml'));

        foreach ($data['objects'] as $objectData) {
            $object = $this->createObject($objectData);
            $manager->persist($object);
        }
        
        $manager->flush();
    }

    function createObject(array $data)
    {
        $object = new ArtObject();      
        $object->setPublished(true);
        $object->setTitle($data['title']);
        $object->setDescription($data['description']);
        $object->setDate(new \DateTime($data['date']));
        
        if(!empty($data['image'])) {
            $object->setImage($this->createMediaImage($data['image']));
        }               

        if (!empty($data['categories'])) {
            foreach ($data['categories'] as $ref) {
                $category = $this->getReference($ref);
                
                if (empty($category)) {
                    throw new \InvalidArgumentException('Category reference ' . $ref . ' not found');
                }
                
                $object->addCategories($category);
            }
        }

        if (!empty($data['authors'])) {
            foreach ($data['authors'] as $ref) {
                $author = $this->getReference($ref);
                
                if (empty($author)) {
                    throw new \InvalidArgumentException('Authors reference ' . $ref . ' not found');
                }
                
                $object->addAuthor($author);
            }
        }
        
        if(!empty($data['museum'])) {
            $museum = $this->getReference($data['museum']);

            if (empty($museum)) {
                throw new \InvalidArgumentException('Museum reference ' . $data['museum'] . ' not found');
            }

            $object->setMuseum($museum);
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
        return 'exhibit';
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
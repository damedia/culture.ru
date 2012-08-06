<?php

namespace Armd\AtlasBundle\Tests\Listener;

use Armd\AtlasBundle\Tests\AbstractFunctionalTest;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Armd\AtlasBundle\DataFixtures\ORM\Test\LoadTestObjectData;

class DefaultControllerTest extends AbstractFunctionalTest
{


    public function testMediaUpdate()
    {
        $imageProvider = $this->container->get('sonata.media.provider.image');

        $object = $this->em->getRepository('ArmdAtlasBundle:Object')->findOneBy(array());
        $media = $object->getImages()->get(0);

        $oldPath = realpath($this->mediaDir . '/' . $imageProvider->getReferenceImage($media));
        $oldCrc = md5(file_get_contents($oldPath));
        $oldCount = $this->countFiles(dirname($oldPath));

        $newFile = LoadTestObjectData::createUploadedFile('new.png');
        $media->setFormImageFile($newFile);
        $this->em->flush();

        $newPath = realpath($this->mediaDir . '/' . $imageProvider->getReferenceImage($media));
        $newCrc = md5(file_get_contents($newPath));
        $newCount = $this->countFiles(dirname($newPath));


        $this->assertFalse(file_exists($oldPath));
        $this->assertNotEquals($oldCrc, $newCrc);
        $this->assertEquals($oldCount, $newCount);

    }

    public function testImage3dRemove()
    {
        $imageProvider = $this->container->get('sonata.media.provider.image');

        $object = $this->em->getRepository('ArmdAtlasBundle:Object')
            ->createQueryBuilder('o')
            ->where('o.image3d IS NOT NULL')
            ->setMaxResults(1)
            ->getQuery()->getSingleResult();

        $media = $object->getImage3d();
        $path = realpath($this->mediaDir . '/' . $imageProvider->getReferenceImage($media));

        $media->setRemoveMedia(true);
        $this->em->flush();

        $this->assertFalse(file_exists($path));
    }

}
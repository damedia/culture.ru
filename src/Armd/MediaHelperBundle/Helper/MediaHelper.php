<?php
namespace Armd\MediaHelperBundle\Helper;

use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaHelper
{
    public static function createMediaImage($fileName, $context)
    {
        $file = self::createUploadedFile($fileName);
        $media = new Media();
        $media->setBinaryContent($file);
        $media->setContext($context);
        $media->setProviderName('sonata.media.provider.image');

        return $media;
    }

    public static function createUploadedFile($fileName)
    {
        $filePath = $fileName;
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException('Media file not found ' . $fileName);
        }

        $file = new UploadedFile($filePath, $fileName);

        return $file;
    }
}


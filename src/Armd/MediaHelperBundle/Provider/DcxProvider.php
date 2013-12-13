<?php
namespace Armd\MediaHelperBundle\Provider;

use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Sonata\MediaBundle\Provider\ImageProvider;

use Imagine\Image\ImagineInterface;
use Gaufrette\Filesystem;
use Armd\DCXBundle\DCX\DCXClient;
use Armd\DCXBundle\DCX\DCXDocument;
use Symfony\Component\HttpFoundation\File\File;

class DcxProvider extends ImageProvider
{
    protected $imagineAdapter;
    protected $dcxClient;

    /**
     * @param string                                           $name
     * @param \Gaufrette\Filesystem                            $filesystem
     * @param \Sonata\MediaBundle\CDN\CDNInterface             $cdn
     * @param \Sonata\MediaBundle\Generator\GeneratorInterface $pathGenerator
     * @param \Sonata\MediaBundle\Thumbnail\ThumbnailInterface $thumbnail
     * @param array                                            $allowedExtensions
     * @param array                                            $allowedMimeTypes
     * @param \Imagine\Image\ImagineInterface                  $adapter
     * @param \Armd\DCXBundle\DCX\DCXClient                    $client
     */
    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, array $allowedExtensions = array(), array $allowedMimeTypes = array(), ImagineInterface $adapter, DCXClient $client)
    {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail, $allowedExtensions, $allowedMimeTypes, $adapter);
        $this->dcxClient = $client;
    }

    /**
     * {@inheritdoc}
     */
    protected function doTransform(MediaInterface $media)
    {
        $providerReference = $media->getPreviousProviderReference();
        if (!$media->getBinaryContent()) {
            return;
        }
        $dcx = $this->dcxClient;
        $res = $dcx->getDoc($media->getBinaryContent());
        if(!$res instanceof DCXDocument){
            throw new \RuntimeException('Изображение с заданным DC ID не найдено в системе. DC_ID: '.$media->getBinaryContent());
        }
        if(!$res->checkFileName('original','Образы России')){
            throw new \RuntimeException('Изображение с заданным DC ID не имеет правильного варианта для выгрузки DCX_ID:' .$media->getBinaryContent());
        }
        $dcx_file_data = $res->getImageFileData('original','Образы России');
        $file = $dcx->getFile($dcx_file_data->path);

        $path       = tempnam(sys_get_temp_dir(), 'sonata_update_metadata');
        $fileObject = new \SplFileObject($path, 'w');
        $fileObject->fwrite($file);
        $binaryContent = new File($fileObject->getPathname());
        $media->setProviderReference($providerReference);
        $media->setBinaryContent($binaryContent);
        
        parent::doTransform($media);

    }


    public function postUpdate(MediaInterface $media)
    {
        parent::postUpdate($media);
        $path = $media->getBinaryContent()->getPathname();
        if (file_exists($path))
        {
            unlink($path);
        }
    }
}

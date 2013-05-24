<?php

namespace Armd\MediaHelperBundle\Provider;

use Sonata\MediaBundle\Provider\BaseVideoProvider;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\MediaBundle\Model\MediaInterface;
use Symfony\Component\Form\Form;
use Gaufrette\Filesystem;
use Sonata\MediaBundle\CDN\CDNInterface;
use Sonata\MediaBundle\Generator\GeneratorInterface;
use Sonata\MediaBundle\Thumbnail\ThumbnailInterface;
use Buzz\Browser;
use Symfony\Component\HttpFoundation\RedirectResponse;


class TvigleProvider extends BaseVideoProvider
{
    protected $api;

    /**
     * @param string                                           $name
     * @param \Gaufrette\Filesystem                            $filesystem
     * @param \Sonata\MediaBundle\CDN\CDNInterface             $cdn
     * @param \Sonata\MediaBundle\Generator\GeneratorInterface $pathGenerator
     * @param \Sonata\MediaBundle\Thumbnail\ThumbnailInterface $thumbnail
     * @param \Buzz\Browser                                    $browser
     * @param array                                            $api
     */
    public function __construct($name, Filesystem $filesystem, CDNInterface $cdn, GeneratorInterface $pathGenerator, ThumbnailInterface $thumbnail, Browser $browser, array $api)
    {
        parent::__construct($name, $filesystem, $cdn, $pathGenerator, $thumbnail, $browser);

        $this->api = $api;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCreateForm(FormMapper $formMapper)
    {
        $formMapper->add('binaryContent', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper)
    {
        $formMapper->add('name');
        $formMapper->add('description', null, array('required' => false));
        $formMapper->add('binaryContent', 'text', array('required' => false));
    }

    /**
     * {@inheritdoc}
     */
    public function getHelperProperties(MediaInterface $media, $format, $options = array())
    {
        $box    = $this->getBoxHelperProperties($media, $format, $options);
        $width  = $box->getWidth();
        $height = $box->getHeight();

        if (!empty($options['width']) and !empty($options['height'])) {
            $width  = $options['width'];
            $height = $options['height'];
        }

        $params = array(
            'src'         => $media->getMetadataValue('frame'),
            'id'          => uniqid('tvigle_player_'),
            'width'       => $width,
            'height'      => $height,
            'frameborder' => isset($options['frameborder']) ? $options['frameborder'] : 0,
            'wmode'       => isset($options['wmode']) ? $options['wmode'] : 'opaque',
            'style'       => isset($options['style']) ? $options['style'] : ''
        );

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    protected function doTransform(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {
            return;
        }

        $media->setProviderName($this->name);
        $media->setProviderStatus(MediaInterface::STATUS_OK);
        $media->setProviderReference($media->getBinaryContent());

        $this->updateMetadata($media, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getDownloadResponse(MediaInterface $media, $format, $mode, array $headers = array())
    {
        return new RedirectResponse(sprintf('http://tvigle.com/%s', $media->getProviderReference()), 302, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(MediaInterface $media, $url = null)
    {
        if (!$media->getBinaryContent()) {
            return;
        }

        $soap = new \SoapClient(
            $this->api['url'],
            array(
                'login'    => $this->api['login'],
                'password' => $this->api['password']
            )
        );

        $metadata = $soap->videoItem($media->getBinaryContent());
        
        if (!$metadata or !$metadata->id) {
            throw new \RuntimeException('Unable to retrieve tvigle video information for :' .$media->getBinaryContent());
        }

        $metadata = (array) $metadata;
        
        if (!$metadata['thumbnail_url'] = $soap->getStopCadrId($media->getBinaryContent())) {
            throw new \RuntimeException('Unable to retrieve tvigle video thumbnail for :' .$media->getBinaryContent());
        }

        try {
            $imgSize = getimagesize($metadata['thumbnail_url']);

        } catch(Exception $e) {
            $imgSize = array(0, 0);
        }

        list($metadata['width'], $metadata['height']) = $imgSize;

        return $metadata;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function updateMetadata(MediaInterface $media, $force = false)
    {
        try {
            $metadata = $this->getMetadata($media);

        } catch (\RuntimeException $e) {
            $media->setEnabled(false);
            $media->setProviderStatus(MediaInterface::STATUS_ERROR);

            return;
        }

        // store provider information
        $media->setProviderMetadata($metadata);

        // update Media common fields from metadata
        if ($force) {
            $media->setName($metadata['name']);
            $media->setDescription($metadata['anons']);
        }

        $media->setWidth($metadata['width']);
        $media->setHeight($metadata['height']);
        $media->setLength($metadata['duration']);
        $media->setContentType('video/x-flv');
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {
            return;
        }

        // retrieve metadata
        $metadata = $this->getMetadata($media);

        // store provider information
        $media->setProviderName($this->name);
        $media->setProviderReference($media->getBinaryContent());
        $media->setProviderMetadata($metadata);

        // update Media common field from metadata
        $media->setName($metadata['name']);
        $media->setDescription($metadata['anons']);
        $media->setWidth($metadata['width']);
        $media->setHeight($metadata['height']);
        $media->setLength($metadata['duration']);
        $media->setContentType('video/x-flv');
        $media->setProviderStatus(MediaInterface::STATUS_OK);

        $media->setCreatedAt(new \Datetime());
        $media->setUpdatedAt(new \Datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {
            return;
        }

        $metadata = $this->getMetadata($media);

        $media->setProviderReference($media->getBinaryContent());
        $media->setProviderMetadata($metadata);
        $media->setProviderStatus(MediaInterface::STATUS_OK);

        $media->setUpdatedAt(new \Datetime());
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(MediaInterface $media)
    {
        $this->postPersist($media);
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {
            return;
        }

        $this->generateThumbnails($media);
    }
}

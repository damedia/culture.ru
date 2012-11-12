<?php
namespace Armd\MediaHelperBundle\Provider;

use Sonata\MediaBundle\Provider\BaseVideoProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\MediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Application\Sonata\MediaBundle\Entity\Media;
use Symfony\Component\Form\Form;

class CultureTvProvider extends BaseVideoProvider
{

    public function getHelperProperties(MediaInterface $media, $format, $options = array())
    {

        $params = array(
            'id'          => isset($options['id']) ? $options['id'] : 0,
            'frameborder' => isset($options['frameborder']) ? $options['frameborder'] : 0,
            'width'       => isset($options['width']) ? $options['width'] : '100%',
            'height'      => isset($options['height']) ? $options['height']: '100%',
        );

        return $params;
    }

    /**
     * @param \Sonata\MediaBundle\Model\MediaInterface $media
     *
     * @return void
     */
    protected function doTransform(MediaInterface $media)
    {
        if (!$media->getBinaryContent()) {
            return;
        }

        // store provider information
        $media->setProviderName($this->name);
        $media->setProviderReference($media->getBinaryContent());
        $media->setProviderStatus(MediaInterface::STATUS_OK);

        $this->updateMetadata($media, true);
    }

    /**
     * Mode can be x-file
     *
     * @param MediaInterface $media
     * @param string         $format
     * @param string         $mode
     * @param array          $headers
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function getDownloadResponse(MediaInterface $media, $format, $mode, array $headers = array())
    {
        return new RedirectResponse($media->getProviderReference(), 302, $headers);
    }

    /**
     * @param MediaInterface $media
     * @param bool           $force
     *
     * @return void
     */
    function updateMetadata(MediaInterface $media, $force = false)
    {
        $url = $media->getProviderReference();

        try {
            $metadata = $this->getMetadata($media, $url);
        } catch (\RuntimeException $e) {
            $media->setEnabled(false);
            $media->setProviderStatus(MediaInterface::STATUS_ERROR);

            return;
        }

        // store provider information
        $media->setProviderMetadata($metadata);

        // update Media common fields from metadata
        if ($force) {
            $media->setName('TV Culture brand_id:' . $metadata['brand_id'] . ' video_cid: ' . $metadata['video_cid']);
            $media->setDescription('');
            $media->setAuthorName('TV Culture');
        }

//        $media->setHeight($metadata['height']);
//        $media->setWidth($metadata['width']);
//        $media->setLength($metadata['duration']);
        $media->setContentType('video/x-flv');
    }



    protected function getMetadata(MediaInterface $media, $url)
    {
        $content = @file_get_contents($url);

        $metadata = array();
        if(preg_match('~<div class="p-pvideo-player">\s+<iframe src="(.+)"~U', $content, $matches)) {
            $metadata['player_url'] = $matches[1];
        } else {
            throw new \RuntimeException('Unable to retrieve culture tv video information for :' . $url);
        }

        if (preg_match('~/brand_id/(\d+)/video_cid/(\d+)~', $url, $matches)) {
            $metadata['brand_id'] = $matches[1];
            $metadata['video_cid'] = $matches[2];
        }

        return $metadata;
    }

    public function requireThumbnails()
    {
        return false;
    }

}
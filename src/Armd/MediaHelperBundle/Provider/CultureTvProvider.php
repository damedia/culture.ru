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
        $media->setProviderStatus(MediaInterface::STATUS_OK);
        $media->setProviderReference($media->getBinaryContent());

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
        try {
            $url = $media->getProviderReference();
            $metadata = $this->getMetadata($media, $url);
        } catch (\RuntimeException $e) {
            $media->setEnabled(false);
            $media->setProviderStatus(MediaInterface::STATUS_ERROR);

            return;
        }

        // store provider information
        $media->setProviderReference($metadata['thumbnail_url']); // not very logical but needed to create thumbnails
        $media->setProviderMetadata($metadata);

        // update Media common fields from metadata
        if ($force) {
            $media->setName('TV Culture brand_id:' . $metadata['brand_id'] . ' video_cid: ' . $metadata['video_cid']);
            $media->setDescription('');
            $media->setAuthorName('TV Culture');
        }

        list($thumbWidth, $thumbHeight) = getimagesize($metadata['thumbnail_url']);

        $media->setHeight($thumbHeight);
        $media->setWidth($thumbWidth);
//        $media->setLength($metadata['duration']);
        $media->setContentType('video/x-flv');
    }



    protected function getMetadata(MediaInterface $media, $url)
    {
        $content = @file_get_contents($url);

        $metadata = array();

        // get player url
        if(preg_match('~<div class="p-pvideo-player">\s+<iframe src="(.+)"~U', $content, $matches)) {
            $metadata['player_url'] = $matches[1];
        } else {
            throw new \RuntimeException('Unable to retrieve culture tv video information for :' . $url);
        }

        // get video id, brand id
        if (preg_match('~/brand_id/(\d+)/video_\w?id/(\d+)~', $url, $matches)) {
            $metadata['brand_id'] = $matches[1];
            $metadata['video_cid'] = $matches[2];
        }

        // get thumbnail url
        $contentIframe = file_get_contents($metadata['player_url']);
        if (preg_match('~var html5 = \{\"picture\":\"(.+)\"~U', $contentIframe, $matches)) {
            $metadata['thumbnail_url'] = stripslashes($matches[1]);
        } else {
            throw new \RuntimeException('Unable to retrieve picture url');
        }

        return $metadata;
    }

}
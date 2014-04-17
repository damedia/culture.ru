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
    public function getHelperProperties(MediaInterface $media, $format, $options = array()) {
        $box = $this->getBoxHelperProperties($media, $format, $options);
        $width = $box->getWidth();
        $height = $box->getHeight();

        if (!empty($options['width']) and !empty($options['height'])) {
            $width  = $options['width'];
            $height = $options['height'];
        }

        /**
         * All this data is from our DB! It's in the table "media__media", field "provider_metadata" (SQL type: text).
         * The result of print_r($media->getProviderMetadata()) call:
         * Array (
         *      [id] => 2372998
         *      [name] => Фестиваль OPEN PLACE – первый кинофестиваль в Резекне (Латвия)
         *      [catalog] => 6034
         *      [anons] => Авторам короткометражных фильмов вручили награды
         *      [tags] =>
         *      [duration] => 222880
         *      [geo] =>
         *      [date] => 2013-08-05T23:26:53+04:00
         *      [img] => http://photo.tvigle.ru/res/prt/85deec0537d438363b1c3b54dbe222e8/29/98/000002372998/pub.jpg
         *      [swf] => http://pub.tvigle.ru/swf/tvigle_single_v2.swf?prt=85deec0537d438363b1c3b54dbe222e8&id=2372998&srv=pub.tvigle.ru&modes=1&nl=1
         *      [frame] => http://pub.tvigle.ru/frame/p.htm?prt=85deec0537d438363b1c3b54dbe222e8&id=2372998&srv=pub.tvigle.ru&modes=1&nl=1
         *      [rs] => 1
         *      [code] =>
         *      [subtitles] => 0
         *      [under] => 0
         *      [mob] => 0
         *      [thumbnail_url] => http://photo.tvigle.ru/res/prt/85deec0537d438363b1c3b54dbe222e8/29/98/000002372998/pub.jpg
         *      [height] => 405
         *      [width] => 720
         * )
         */

        $params = array(
            'src'         => $media->getMetadataValue('frame'), //old tvigle API JSON parameter
            'embed_html'  => $media->getMetadataValue('embed_html'), //new tvigle API JSON parameter
            'id'          => uniqid('tvigle_player_'),
            'width'       => $width,
            'height'      => $height,
            'frameborder' => isset($options['frameborder']) ? $options['frameborder'] : 0,
            'wmode'       => isset($options['wmode']) ? $options['wmode'] : 'opaque',
            'style'       => isset($options['style']) ? $options['style'] : '',
            'videoId'     => $media->getMetadataValue('id')
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

        /**
         * We are getting here once we add a Media entity in admin dashboard.
         * For instance: add new 'Media Lecture Video' inside the Video*Video section.
         */

        /*=========================================
         * SOAP call code (old tvigle API):
         *

         $soap = new \SoapClient(
             $this->api['url'],
             array(
                 'login'    => $this->api['login'],
                 'password' => $this->api['password']
             )
         );

         $metadata = $soap->videoItem($media->getBinaryContent());

         *
         * Calling "print_r($metadata);" will give us something like:
         *
         * stdClass Object (
         *      [id] => 2417314
         *      [name] => «Культбюро» - 2013
         *      [catalog] => 6330
         *      [anons] => Лаборатория сценариев, короткого метра и редактуры
         *      [tags] =>
         *      [duration] => 293480
         *      [geo] =>
         *      [date] => 2013-11-05T12:02:01+04:00
         *      [img] => http://photo.tvigle.ru/res/prt/85deec0537d438363b1c3b54dbe222e8/73/14/000002417314/pub.jpg
         *      [swf] => http://pub.tvigle.ru/swf/tvigle_single_v2.swf?prt=85deec0537d438363b1c3b54dbe222e8&id=2417314&srv=pub.tvigle.ru&modes=1&nl=1
         *      [frame] => http://pub.tvigle.ru/frame/p.htm?prt=85deec0537d438363b1c3b54dbe222e8&id=2417314&srv=pub.tvigle.ru&modes=1&nl=1
         *      [rs] => 1
         *      [code] =>
         *      [subtitles] => 0
         *      [under] => 0
         *      [mob] => 0
         * )
         =========================================*/



        /*=========================================
         * REST call code (new tvigle API):
         *
         * Calling "print_r($metadata);" will give us something like:
         *
         * stdClass Object (
         *      [embed_html] => <iframe src=\"http://cloud.tvigle.ru/video/4862607/?\" width=\"702\" height=\"405\" frameborder=\"no\" scrolling=\"no\" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>
         *      [callback_url] =>
         *      [duration] => 104:17
         *      [id] => 4862607
         *      [category] =>
         *      [errors] => Array ( )
         *      [country_restrictions] => Array ( )
         *      [age_restrictions] => 0+
         *      [progress] => 100
         *      [thumbnail] => http://photo.tvigle.ru/res/2014/01/09/f539173f-c0f9-4e69-b007-c0673c7696b4.png
         *      [resources] => Array (
         *          [0] => stdClass Object (
         *              [status] => finished
         *              [progress] => 100
         *              [errors] => Array ( )
         *              [quality] => 720p
         *              [id] => 510582
         *          )
         *          [1] => stdClass Object (
         *              ...
         *          )
         *          ...
         *      )
         *      [status] => published
         *      [description] =>
         *      [tags] => Array ( )
         *      [iframe_url] => http://cloud.tvigle.ru/video/4862607/?
         *      [show_ad] =>
         *      [embed_object] =>
         *      [is_active] => 1
         *      [is_playable] => 1
         *      [duration_in_ms] => 6257920
         *      [publication_date] =>
         *      [status_display] => опубликован
         *      [name] => Душа России
         *      [url] => /client/videos/4862607/
         *      [created_at] => 2014-01-09 14:13:35
         *      [aspect_ratio] => 16:9
         *      [distribution] => 1
         *      [freezeFrame] => http://photo.tvigle.ru/res/2014/01/09/0c37fb66-e4d7-4c6d-b75e-aed80d27a418.png
         * )
         =========================================*/

        //TODO: We may want to extract URL string into config. We also may want to create 'REST service' ot encapsulate call logic details.
        $service_url = 'http://cloud.tvigle.ru/api/videos/'.$media->getBinaryContent().'/?access_token='.$this->api['token'];
        $curl = curl_init($service_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);

        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            throw new \RuntimeException('Unable to retrieve tvigle video information for: '.$media->getBinaryContent().' (additional info: '.$info.')');
        }

        curl_close($curl);
        $metadata = (array) json_decode($curl_response);

        $metadata['thumbnail_url'] = $metadata['thumbnail'];

        try {
            $imgSize = getimagesize($metadata['thumbnail_url']);
        }
        catch(\Exception $e) {
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
        }
        catch (\RuntimeException $e) {
            $media->setEnabled(false);
            $media->setProviderStatus(MediaInterface::STATUS_ERROR);

            return;
        }

        // store provider information
        $media->setProviderMetadata($metadata);

        // update Media common fields from metadata
        if ($force) {
            $media->setName($metadata['name']);
            $media->setDescription($metadata['description']);
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
        $media->setDescription($metadata['description']);
        $media->setWidth($metadata['width']);
        $media->setHeight($metadata['height']);
        $media->setLength($metadata['duration_in_ms']);
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

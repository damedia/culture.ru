<?php

namespace Armd\MediaHelperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MediaController extends Controller {
    /**
     * @Route("/admin/armd/media/media/{id}/{format}", defaults={"format": "reference"})
     */
    public function mediaAction($id, $format) {
        echo $this->getTtwigMediaExtension()->media($id, $format);
        exit;
    }

    /**
     * @Route("/admin/armd/media/path/{id}/{format}", defaults={"format": "reference"})
     */
    public function pathAction($id, $format) {
        echo $this->getTtwigMediaExtension()->path($id, $format);
        exit;
    }

    /**
     * @return TwigExtension
     */
    protected function getTtwigMediaExtension() {
        $twigMediaExtension = $this->container->get('sonata.media.twig.extension');
        $twigMediaExtension->initRuntime($this->get('twig'));

        return $twigMediaExtension;
    }
}
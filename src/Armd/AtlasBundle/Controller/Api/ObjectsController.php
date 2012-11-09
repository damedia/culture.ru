<?php

namespace Armd\AtlasBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use FOS\RestBundle\Controller\Annotations as Rest;

class ObjectsController extends Controller
{
    protected function assembleObjectArray($object)
    {
        $baseUrl = $this->getRequest()->getScheme().'://'.$this->getRequest()->getHost();
        $mediaExtension = $this->get('sonata.media.twig.extension');
        $iconUrl = $object->getIcon()
                 ? $baseUrl.$mediaExtension->path($object->getIcon(), 'reference')
                 : '';
        $obraz = false;
        $imageUrl = '';
        if ($object->getPrimaryCategory()) {
            if ($object->getPrimaryCategory()->getId() == 74) {
                $obraz = true;
                $image = $object->getPrimaryImage();
                $imageUrl = $baseUrl.$mediaExtension->path($image, 'thumbnail');
            }
        }
        return array(
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'announce' => $object->getAnnounce(),
            'lon' => $object->getLon(),
            'lat' => $object->getLat(),
            'obraz' => $obraz,
            'imageUrl' => $imageUrl,
            'iconUrl' => $iconUrl,
        );
    }

    /**
     * @Route("/objects/{id}", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function getAction($id)
    {
        try {
            $objectRepo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
            $object = $objectRepo->find($id);

            if (! $object)
                throw new \Exception('Object not found');

            $result = $this->assembleObjectArray($object);

            return new Response(json_encode(array(
                'success' => true,
                'message' => 'OK',
                'result' => $result,
            )), 200, array('Content-Type'=>'application/json'));
        }
        catch (\Exception $e) {
            return new Response(json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
            )), 200, array('Content-Type'=>'application/json'));
        }
    }

    /**
     * @Route("/objects")
     * @Method("GET")
     */
    public function indexAction()
    {
        try {
            $objectRepo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
            $objects = $objectRepo->findAll();

            if (! $objects)
                throw new \Exception('Objects not found');

            $result = array();

            foreach ($objects as $obj) {
                $result[] = $this->assembleObjectArray($obj);
            }

            return new Response(json_encode(array(
                'success' => true,
                'message' => 'OK',
                'result' => $result,
            )), 200, array('Content-Type'=>'application/json'));
        }
        catch (\Exception $e) {
            return new Response(json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
            )), 200, array('Content-Type'=>'application/json'));
        }
    }

    /**
     * @Route("/objects/create")
     * @Method("POST")
     */
    public function createAction()
    {
    }
}
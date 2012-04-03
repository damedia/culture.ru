<?php

namespace Armd\CultureMapBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Armd\Bundle\CmsBundle\Controller\Controller;
use Armd\Bundle\CmsBundle\UsageType\UsageType;


class CultureMapController extends Controller
{
    /**
     * Display map.
     */
    public function listAction()
    {
        return $this->renderCms(array(
            'name' => 'Markiros',
        ));
    }

    public function regionsAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entities = $em->getRepository('ArmdRegionBundle:Region')->findAll();
        $regions = array();
        foreach ($entities as $e) {
            $regions[] = array(
                'id' => $e->getId(),
                'title' => $e->getTitle(),
            );
        }
        $jsonData = array(
            'success' => true,
            'result' => $regions,
        );
        return new Response(
            json_encode($jsonData),
            $status = 200,
            array('Content-Type' => 'application/json')
        );
    }

    public function regionAction()
    {
        $request = $this->container->get('request');
        $id = $request->query->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        try {
            $entity = $em->getRepository('ArmdCultureMapBundle:Subject')->findOneBy(array(
                'yname' => $id,
            ));
            $gallery = $entity->getGallery();
            if (! $gallery)
                throw new \Exception('No gallery');
            $images = $gallery->getGalleryHasMedias();
            $galleryRandomImage = false;
            $idx = rand(0, sizeof($images)-1);
            foreach ($images as $i=>$image) {
                if ($i==$idx) {
                    $galleryRandomImage = $image;
                    break;
                }
            }
            return $this->render('ArmdCultureMapBundle:CultureMap:region.html.twig', array(
                'entity' => $entity,
                'galleryRandomImage' => $galleryRandomImage,
            ));
        }
        catch (\Exception $e) {
            return new Response(
                json_encode(array(
                    'success' => false,
                    'message' => $e->getMessage(),
                )),
                $status = 404,
                array('Content-Type' => 'application/json')
            );
        }
    }

    public function markersAction()
    {
        $request = $this->container->get('request');
        $id = $request->query->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        try {
            $entityList = $em->getRepository('ArmdCultureMapBundle:CultureObject')->findAll();
            if (! $entityList)
                throw new \Exception('Not found');

            $markers = array();
            foreach ($entityList as $item) {
                $markers[] = array(
                    'title' => $item->getTitle(),
                    'lat' => $item->getLatitude(),
                    'lng' => $item->getLongitude(),
                );
            }
            return new Response(
                json_encode(array(
                    'success' => true,
                    'result' => $markers,
                )),
                $status = 200,
                array('Content-Type' => 'application/json')
            );
        }
        catch (\Exception $e) {
            return new Response(
                json_encode(array(
                    'success' => false,
                    'message' => $e->getMessage(),
                )),
                $status = 200,
                array('Content-Type' => 'application/json')
            );
        }
    }

    public function subjectAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ArmdCultureMapBundle:Subject')->find($id);
        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Subject entity.');
        }
        return $this->renderCms(array(
            'entity' => $entity,
        ));
    }

}

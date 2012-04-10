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
            $galleryRandomImage = false;
            $gallery = $entity->getGallery();
            if ($gallery) {
                $images = $gallery->getGalleryHasMedias();
                $idx = rand(0, sizeof($images)-1);
                foreach ($images as $i=>$image) {
                    if ($i==$idx) {
                        $galleryRandomImage = $image;
                        break;
                    }
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
                    'id' => $item->getId(),
                    'title' => $item->getTitle(),
                    'lat' => $item->getLatitude(),
                    'lng' => $item->getLongitude(),
                    'icon' => $item->getType()->getId(),
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

    public function subjectAction($id=null)
    {
        $isAjax = false;
        if (! $id){
            $request = $this->container->get('request');
            $id = (int) $request->query->get('id');
            $isAjax = true;
        }
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ArmdCultureMapBundle:Subject')->find($id);
        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Subject entity.');
        }
        if (! $isAjax) {
            return $this->renderCms(array(
                'entity' => $entity,
            ));
        } else {
            return $this->render('ArmdCultureMapBundle:CultureMap:subject.html.twig', array(
                'entity' => $entity,
            ));
        }
    }

    public function objectAction()
    {
        $request = $this->container->get('request');
        $id = (int) $request->query->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ArmdCultureMapBundle:CultureObject')->find($id);
        if (! $entity) {
            throw $this->createNotFoundException('Unable to find CultureObject entity.');
        }
        return $this->render('ArmdCultureMapBundle:CultureMap:object.html.twig', array(
            'entity' => $entity,
        ));
    }

}

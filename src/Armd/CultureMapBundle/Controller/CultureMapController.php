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
    public function listAction(UsageType $params)
    {
        return $this->renderCms($params, array(
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
        $entity = $em->getRepository('ArmdCultureMapBundle:Subject')->findOneBy(array(
            'yname' => $id,
        ));
        if ($entity) {
            return $this->render('ArmdCultureMapBundle:CultureMap:region.html.twig', array(
                'entity' => $entity,
            ));
        }
        else {
            return new Response(
                json_encode(array(
                    'success' => false,
                    'message' => 'Not found',
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
        $entityList = $em->getRepository('ArmdCultureMapBundle:CultureObject')->findAll();
        if ($entityList) {
            $markers = array();
            foreach ($entityList as $item) {
                //var_dump($item);
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
        else {
            return new Response(
                json_encode(array(
                    'success' => false,
                    'message' => 'Not found',
                )),
                $status = 404,
                array('Content-Type' => 'application/json')
            );
        }
    }

    public function subjectAction(UsageType $params, $id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ArmdCultureMapBundle:Subject')->find($id);
        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Subject entity.');
        }
        return $this->renderCms($params, 'ArmdCultureMapBundle', 'CultureMap', 'subject', array(
            'entity' => $entity,
        ));
    }

}

<?php

namespace Armd\CultureMapBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Armd\ContentAbstractBundle\Controller\Controller;

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
            // Учреждения
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

            /*
            // Памятники
            $entityList = $em->getRepository('ArmdCommonBundle:Monument')->findAll();
            if (! $entityList)
                throw new \Exception('Not found');

            foreach ($entityList as $item) {
                $markers[] = array(
                    'id' => $item->getId(),
                    'title' => $item->getTitle(),
                    'lat' => $item->getLatitude(),
                    'lng' => $item->getLongitude(),
                    'icon' => 1,
                );
            }
            */

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

        $query = $em->createQuery('
            SELECT 
                o 
            FROM
                ArmdCultureMapBundle:CultureObject o,
                ArmdCultureMapBundle:CultureObjectType t
            WHERE
                o.type = t.id
                and o.subject = :subject_id
            ORDER BY
                t.title, o.title
        ')->setParameter('subject_id', $id);
        
        $objects = $query->getResult();        

        if (! $entity) {
            throw $this->createNotFoundException('Unable to find Subject entity.');
        }
        if (! $isAjax) {
            return $this->renderCms(array(
                'entity' => $entity,
            ));
        } else {
            return $this->render('ArmdCultureMapBundle:CultureMap:subject.html.twig', array(
                'entity'    => $entity,
                'objects'   => $objects,
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

    public function checkAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('ArmdCultureAreaBundle:CultureArea');

        $query = $em
            ->createQueryBuilder()
            ->select('node')
            ->from('ArmdCultureAreaBundle:CultureArea', 'node')
            ->orderBy('node.root, node.lft', 'ASC')
            ->where('node.root = 36')
            ->getQuery()
        ;
        $options = array('decorate' => true);
        $tree = $repo->buildTree($query->getArrayResult(), $options);
        print $tree;

        exit;

        $res = $repo->recover();
        $em->clear();
        var_dump($res);
    }

    /**
     * Новый атлас
     */
    public function atlasAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        // Список регионов
        $dql = "SELECT s FROM ArmdCultureMapBundle:Subject s ORDER BY s.title ASC";
        $regions = $em->createQuery($dql)->getResult();

        // Список типов объектов
        $query = $em->createQueryBuilder()
            ->select(array('t.id, t.title, t.icon', 'COUNT(o.id) AS objectsCount'))
            ->from('ArmdCultureMapBundle:CultureObjectType', 't')
            ->leftJoin('t.objects', 'o')
            ->where("t.icon != ''")
            ->groupBy('t.id, t.title, t.icon')
            ->orderBy('objectsCount', 'DESC')
            ->getQuery();
        //print $query->getDql();
        $objectTypes = $query->getResult();

        return $this->renderCms(array(
            'regions' => $regions,
            'objectTypes' => $objectTypes,
        ));
    }

    public function testmarkersAction()
    {
        $searchTerm = trim($this->get('request')->get('search'));
        $regionId = (int) $this->get('request')->get('region');
        $typeIds = $this->get('request')->get('type');

        $em = $this->getDoctrine()->getEntityManager();

        if (! $typeIds) {
            $markers = array();
        } else {
            $qb = $em->createQueryBuilder()
                ->select('o')
                ->from('ArmdCultureMapBundle:CultureObject', 'o');

            if (is_array($typeIds)) {
                $qb->andwhere('o.type IN (:typeIds)')
                   ->setParameter('typeIds', $typeIds);
            }
            if ($regionId) {
                $qb->andWhere('o.subject = :regionId')
                   ->setParameter('regionId', $regionId);
            }
            if ($searchTerm) {
                $qb->andWhere('LOWER(o.title) LIKE :searchTerm')
                   ->setParameter('searchTerm', '%'.$searchTerm.'%');
            }

            $qb->orderBy('o.id', 'ASC');

            $query = $qb->getQuery();

            $objects = $query->getResult();
            $markers = array();
            foreach ($objects as $item) {
                $markers[] = array(
                    'id' => $item->getId(),
                    'title' => $item->getTitle(),
                    'lat' => $item->getLatitude(),
                    'lng' => $item->getLongitude(),
                    'type' => $item->getType()->getId(),
                );
            }
        }

        $data = array(
            'success' => true,
            'result' => $markers,
        );

        return new Response(json_encode($data));
    }

    public function testmarkerdetailAction()
    {
        $request = $this->container->get('request');
        $id = (int) $this->get('request')->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('ArmdCultureMapBundle:CultureObject')->find($id);
        if (! $entity) {
            throw $this->createNotFoundException('Unable to find CultureObject entity.');
        }
        return $this->render('ArmdCultureMapBundle:CultureMap:testmarkerdetail.html.twig', array(
            'entity' => $entity,
        ));
    }

    public function testregiondetailAction()
    {
        $request = $this->container->get('request');
        $id = (int) $request->query->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        try {
            $entity = $em->getRepository('ArmdCultureMapBundle:Subject')->findOneBy(array(
                'id' => $id,
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
            return $this->render('ArmdCultureMapBundle:CultureMap:testregiondetail.html.twig', array(
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
    
}

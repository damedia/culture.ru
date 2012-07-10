<?php

namespace Armd\AtlasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // Список регионов
        $dql = "SELECT s FROM ArmdAtlasBundle:Subject s ORDER BY s.title ASC";
        $regions = $em->createQuery($dql)->getResult();

        // Список типов объектов
        $query = $em->createQueryBuilder()
            ->select(array('t.id, t.title, t.icon', 'COUNT(o.id) AS objectsCount'))
            ->from('ArmdAtlasBundle:CultureObjectType', 't')
            ->leftJoin('t.objects', 'o')
            ->where("t.icon != ''")
            ->groupBy('t.id, t.title, t.icon')
            ->orderBy('objectsCount', 'DESC')
            ->getQuery();
        //print $query->getDql();
        $objectTypes = $query->getResult();

        return array(
            'regions' => $regions,
            'objectTypes' => $objectTypes,
        );
    }

    /**
     * @Route("/objects")
     */
    public function objectsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:CultureObject');

        $res = $repo->findAll();

        $entities = array();
        foreach ($res as $entity) {
            $entities[] = array(
                'id' => $entity->getId(),
                'title' => $entity->getTitle(),
                'announce' => $entity->getAnnounce(),
                'text' => $entity->getText(),
                'lat' => $entity->getLatitude(),
                'lon' => $entity->getLongitude(),
            );
        }

        $response = new Response(json_encode($entities));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/proxy")
     */
    public function proxyAction()
    {
        $requestQuery = $this->getRequest()->getQueryString();

        $url = 'http://mkprom.dev.armd.ru/_sys/map/testmarkers';
        $username = 'admin';
        $password = '6fbff2d72a7aa45a0cb50913094b9bdc';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url.'?'.$requestQuery);
        curl_setopt($ch, CURLOPT_USERPWD, $username.':'.$password);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = new Response($result);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}

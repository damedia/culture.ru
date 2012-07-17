<?php

namespace Armd\AtlasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Buzz\Browser;
use Armd\AtlasBundle\Entity\Category;

class DefaultController extends Controller
{
    protected $testmarkersUrl = 'http://mkprom.dev.armd.ru/_sys/map/testmarkers';
    protected $detailsUrl     = 'http://mkprom.dev.armd.ru/_sys/map/testmarkerdetail';
    protected $username = 'admin';
    protected $password = '6fbff2d72a7aa45a0cb50913094b9bdc';

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
        $request = $this->getRequest();
        $categoryIds = array_keys($request->get('category'));
        $searchTerm = $request->get('q');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Object');

        $res = $repo->search(array(
            'term' => $searchTerm,
            'category' => $categoryIds,
        ));

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
     * @Route("/object/{id}")
     * @Template()
     */
    public function objectViewAction($id)
    {
        $repo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
        $entity = $repo->find($id);

        return array(
            'entity' => $entity,
        );
    }

    /**
     * @Route("/proxy")
     */
    public function proxyAction()
    {
        $requestQuery = $this->getRequest()->getQueryString();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $this->testmarkersUrl.'?'.$requestQuery);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = new Response($result);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/proxydetail")
     */
    public function proxydetailAction()
    {
        $requestQuery = $this->getRequest()->getQueryString();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $this->detailsUrl.'?'.$requestQuery);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
        $result = curl_exec($ch);
        curl_close($ch);

        $response = new Response($result);
        $response->headers->set('Content-Type', 'text/html');
        return $response;
    }

    /**
     * @Route("/calcroute")
     */
    public function calcRouteAction()
    {
        $progorodApiKey = $this->container->getParameter('progorod_api_key');
        $params = array(
            'n' => 3,
            'type' => 'route,plan,indexes',
            'method' => 'optimal',
            'p0x' => 37.42362404792954,
            'p0y' => 54.94441026601353,
            'p1x' => 39.741739282328965,
            'p1y' => 54.61419589978249,
            'p2x' => 39.511026391701535,
            'p2y' => 55.55940194740992,
        );
        $url = 'http://route.tmcrussia.com/cgi/getroute?'.http_build_query($params).'&'.$progorodApiKey;

        $browser = new Browser();
        $response = $browser->get($url);

        return new Response($response->getContent());
    }

    /**
     * @Route("/routes")
     * @Template()
     */
    public function routesAction()
    {
        return array();
    }

    /**
     * @Route("/gmaps")
     * @Template()
     */
    public function gmapsAction()
    {
        return array();
    }

    /**
     * @Route("/new")
     * @Template()
     */
    public function newAction()
    {
        return array();
    }

    protected function getUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    /**
     * @Route("/addnode/{parentId}")
     * Usage: http://local.armd.ru/app_dev.php/atlas/addnode/1?title=%D0%9E%D0%B1%D1%8A%D0%B5%D0%BA%D1%82%D1%8B%20%D0%BA%D1%83%D0%BB%D1%8C%D1%82%D1%83%D1%80%D0%BD%D0%BE%D0%B3%D0%BE%20%D0%BD%D0%B0%D1%81%D0%BB%D0%B5%D0%B4%D0%B8%D1%8F
     */
    public function addNodeAction($parentId)
    {
        $title = $this->getRequest()->query->get('title');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Category');

        $entity = new Category();
        $entity->setTitle($title);

        $parent = $repo->find($parentId);
        if ($parent) {
            $entity->setParent($parent);
        }

        $em->persist($entity);
        $em->flush();

        $response = '';
        return new Response($response);
    }


}

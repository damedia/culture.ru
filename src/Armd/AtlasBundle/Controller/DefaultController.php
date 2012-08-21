<?php

namespace Armd\AtlasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Buzz\Browser;
use Armd\AtlasBundle\Entity\Category;
use Armd\AtlasBundle\Entity\Object;
use Armd\AtlasBundle\Form\ObjectType;

class DefaultController extends Controller
{
    protected $testmarkersUrl = 'http://mkprom.dev.armd.ru/_sys/map/testmarkers';
    protected $detailsUrl     = 'http://mkprom.dev.armd.ru/_sys/map/testmarkerdetail';
    protected $username = 'admin';
    protected $password = '6fbff2d72a7aa45a0cb50913094b9bdc';

    /**
     * @Route("/rebuild_tree")
     */
    public function rebuildTreeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repairer = new \Armd\AtlasBundle\Util\TreeRepairer();
        $repairer->rebuildTree($em, $em->getRepository('ArmdAtlasBundle:Category'));
        return new Response('ok');
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
     * @Route("/object/{id}", requirements={"id"="\d+"}, name="armd_atlas_default_object_view")
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
     * @Route("/object/balloon")
     * @Template()
     */
    public function objectBalloonAction()
    {
        $id = (int) $this->getRequest()->query->get('id');
        $repo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
        $entity = $repo->find($id);

        return array(
            'entity' => $entity,
        );
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
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Category');
        $categories = $repo->getDataForFilter();
        return array(
            'categories' => $categories,
        );
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

    /**
     * @Route("/object/{id}/edit")
     * @Template("ArmdAtlasBundle:Default:objectEdit.html.twig")
     */
    public function editAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object')->find($id);
        $form = $this->createForm(new ObjectType(), $entity);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('armd_atlas_default_list'));
            }
        }

        return array(
            'form' => $form->createView(),
            'entity' => $entity,
        );
    }

    /**
     * @Route("/objects/create")
     * @Template("ArmdAtlasBundle:Default:objectCreate.html.twig")
     */
    public function createAction()
    {
        $entity = new Object();
        $form = $this->createForm(new ObjectType(), $entity);
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST') {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('armd_atlas_default_edit', array('id' => $entity->getId())));
            }
        }

        return array(
            'form' => $form->createView(),
            'entity' => $entity,
        );
    }

    /**
     * @Route("/objects/list")
     * @Template("ArmdAtlasBundle:Default:objectList.html.twig")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery("SELECT o FROM ArmdAtlasBundle:Object o ORDER BY o.id ASC");
        $entities = $query->getResult();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * @Route("/objects/{id}/delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Object');

        $entity = $repo->find($id);

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('armd_atlas_default_list'));
    }

    /**
     * @Route("/objects/import")
     * @Template("ArmdAtlasBundle:Default:objectImport.html.twig")
     */
    public function importAction()
    {
        if ($this->getRequest()->getMethod() == 'POST') {

            $userfile = $_FILES['userfile'];

            $rows = file($userfile['tmp_name']);
            $data = array();
            foreach ($rows as $row) {
                $row = trim($row);
                $rowData = str_getcsv($row, ';', '"');
                $data[] = $rowData;
            }
            //var_dump($data);


            //------------
            $em = $this->getDoctrine()->getManager();
            //$repo = $em->getRepository('ArmdAtlasBundle:Object');

            foreach ($data as $row) {

                $entity = new Object();
                $entity->setTitle(trim($row[5]));
                if ($row[11] != '') $entity->setLat($row[11]);
                if ($row[12] != '') $entity->setLon($row[12]);
                $entity->setAnnounce(trim($row[6]));
                $entity->setContent(trim($row[7]));
                $entity->setSiteUrl(trim($row[8]));
                $entity->setEmail(trim($row[9]));
                $entity->setPhone(trim($row[10]));
                if ($row[13]) $entity->setAddress(trim($row[13]));

                $em->persist($entity);
                $em->flush();
            }
        }

        return array();
    }

    /**
     * @Route("/objects/filter")
     */
    public function filterAction()
    {
        $request = $this->getRequest();
        try {
            $category = $request->get('category');
            if (empty($category))
                throw new \Exception('Categories is null');

            $categoryIds = explode(',', $category);

            if (empty($categoryIds))
                throw new \Exception('Categories is null');

            $filterParams = array(
                'term' => '',
                'category' => $categoryIds,
            );

            $repo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
            $res = $repo->filter($filterParams);

            $rows = array();
            $mediaHelper = $this->get('armd_media_helper.twig_extension.media_helper');
            foreach ($res as $obj) {
                $rows[] = array(
                    'id' => $obj->getId(),
                    'title' => $obj->getTitle(),
                    'announce' => $obj->getAnnounce(),
                    'lon' => $obj->getLon(),
                    'lat' => $obj->getLat(),
                    'icon' => $mediaHelper->originalUrl($obj->getIcon()),
                );
            }

            $response = json_encode(array(
                'success' => true,
                'result' => $rows,
            ));
            return new Response($response);
        }
        catch (\Exception $e) {
            $response = json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
            ));
            return new Response($response);
        }
    }

    /**
     * Метод перемещает узлы категорий. source в target
     *
     * @Route("/category/move")
     */
    public function moveCategoryAction()
    {
        $source = (int) $this->getRequest()->query->get('source');
        $target = (int) $this->getRequest()->query->get('target');

        var_dump($source, $target);

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Category');

        $sourceNode = $repo->find($source);
        $targetNode = $repo->find($target);

        $sourceNode->setParent($targetNode);
        $em->persist($sourceNode);
        $em->flush();

        $response = 'OK';
        return new Response($response);
    }

    /**
     * @Route("/objects/geocoder")
     */
    public function geocoderAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Object');

        $objects = $repo->findAll();
        foreach ($objects as $object) {
            if ($object->getAddress() && !$object->getLat()) {
                $point = $this->resolveAddress($object->getAddress());

                if ($point) {
                    $object->setLat($point['lat']);
                    $object->setLon($point['lon']);

                    $em->persist($object);
                    //$em->flush();
                }
            }
        }

        $response = 'OK';
        return new Response($response);
    }

    /**
     * @Route("/category/{id}/delete")
     */
    public function categoryDeleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Category');

        $entity = $repo->find($id);
        $em->remove($entity);
        $em->flush();

        $response = 'OK';
        return new Response($response);
    }

    protected function resolveAddress($geocode)
    {
        $url = "http://geocode-maps.yandex.ru/1.x/?geocode=" . urlencode($geocode);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s

        $result = curl_exec($ch); // run the whole process
        if ($result === false) {
            var_dump($url);
            var_dump(curl_error($ch));
        }

        curl_close($ch);

        $xml = simplexml_load_string($result);

        $res = array();
        foreach ($xml->GeoObjectCollection->featureMember as $featureMember) {
            $latLng = (string) $featureMember->GeoObject->Point->pos;
            list($lat, $lon) = explode(' ', $latLng);
            $point = array(
                'name' => (string) $featureMember->GeoObject->name,
                'description' => (string) $featureMember->GeoObject->description,
                'lat' => $lat,
                'lon' => $lon,
            );
            $res[] = $point;
        }

        return ! empty($res) ? $res[0] : false;
    }
}
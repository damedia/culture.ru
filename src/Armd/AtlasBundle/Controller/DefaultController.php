<?php

namespace Armd\AtlasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Buzz\Browser;
use Armd\AtlasBundle\Entity\Category;
use Armd\AtlasBundle\Entity\Object;
use Armd\AtlasBundle\Form\ObjectType;

class DefaultController extends Controller
{
    protected $testmarkersUrl = 'http://mkprom.dev.armd.ru/_sys/map/testmarkers';
    protected $detailsUrl = 'http://mkprom.dev.armd.ru/_sys/map/testmarkerdetail';
    protected $username = 'admin';
    protected $password = '6fbff2d72a7aa45a0cb50913094b9bdc';


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
        $entity = $repo->findOneBy(array(
            'published' => true,
            'id' => $id,
        ));

//        $tokenUrl = $this->generateUrl($this->container->getParameter('security.loginza.token_route'), array(), true);

        if ($entity)
            return array(
                'entity' => $entity,
//                'tokenUrl' => $tokenUrl
            );
        else
            throw new NotFoundHttpException("Page not found");
    }

    /**
     * @Route("/user-objects", name="armd_atlas_user_objects")
     * @Template()
     */
    public function userObjectsAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $userObjects = $this->get('armd_atlas.manager.object')->getUserObjects($user);

        return array(
            'user' => $user,
            'userObjects' => $userObjects
        );
    }

    /**
     * @Route("/user-object/{objectId}", requirements={"objectId"="\d+"}, name="armd_atlas_user_object")
     * @Template()
     */
    public function userObjectAction($objectId)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');

        // redirect to edit mode
        if($request->get('btn_return_to_list')) {
            return $this->redirect($this->generateUrl('armd_atlas_user_objects'));
        }

        $object = $em->getRepository('ArmdAtlasBundle:Object')->find($objectId);
        if(empty($object)) {
            throw new \InvalidArgumentException('Object not found');
        }

        $securityContext = $this->get('security.context');
        if(false === $securityContext->isGranted('EDIT', $object)) {
            throw new AccessDeniedException();
        }

        $objectAdmin = $this->get('armd_atlas.sonata_admin.object');
        if ($request->get('uniqid')) {
            $objectAdmin->setUniqid($request->get('uniqid'));
        }
        $objectAdmin->setSubject($object);


        $form = $objectAdmin->getForm();
        $form->setData($object);

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            $isFormValid = $form->isValid();

            if ($isFormValid) {
                $objectAdmin->update($object);
                $this->get('session')->setFlash('sonata_flash_success', 'flash_edit_success');
                $em->flush();
                return $this->redirect($this->generateUrl('armd_atlas_user_object', array('objectId' => $object->getId())));

            } else {
                $this->get('session')->setFlash('sonata_flash_error', 'flash_edit_error');
            }
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $objectAdmin->getFormTheme());


        return array(
            'form' => $view,
            'admin' => $objectAdmin,
            'object' => $object,
        );

    }

    /**
     * @Route("russia-images", name="armd_atlas_russia_images")
     * @Template()
     */
    public function russiaImagesAction()
    {
        return array();
    }

    /**
     * @Route("russia-images-list", name="armd_atlas_russia_images_list", options={"expose"=true})
     * @Template()
     */
    public function russiaImagesListAction()
    {
        $searchString = $this->getRequest()->get('searchString');
        $objectRepo = $this->getDoctrine()->getManager()
                        ->getRepository('ArmdAtlasBundle:Object');

        if (!empty($searchString)) {
            $search = $this->get('search.sphinxsearch.search');
            $searchParams = array(
                'Atlas' => array(
                    'filters' => array(
                        array(
                            'attribute' => 'show_at_russian_image',
                            'values' => array(1)
                        ),
                        array(
                            'attribute' => 'published',
                            'values' => array(1)
                        )
                    )
                )
            );

            $searchResult = $search->search($searchString, $searchParams);
            $objects = array();
            if (!empty($searchResult['Atlas']['matches'])) {
                foreach ($searchResult['Atlas']['matches'] as $id => $data) {
                    $object = $objectRepo->find($id);
                    if(!empty($object)) {
                        $objects[] = $object;
                    }
                }
            }

        }
        else {
            $objects = $objectRepo->findRussiaImages();
        }


        return array(
            'objects' => $objects
        );
    }

    /**
     * @Route("/object/balloon")
     * @Template()
     */
    public function objectBalloonAction()
    {
        $id = (int)$this->getRequest()->query->get('id');
        $repo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
        if ($id) {
            $entity = $repo->findOneBy(array('id'=>$id, 'published'=>true));
            if ($entity)
                return array(
                    'entity' => $entity,
                );
            else
                throw new NotFoundHttpException("Page not found");
        }
    }

    /**
     * @Route("/object/cluster")
     * @Template()
     */
    public function clusterBalloonAction()
    {
        $ids = $this->getRequest()->query->get('ids');
        $repo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
        if ($ids) {
            $entities = $repo->findBy(array(
                'published' => true,
                'id' => $ids,
            ));
            return array(
                'entities' => $entities,
            );
        }
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
        $url = 'http://route.tmcrussia.com/cgi/getroute?' . http_build_query($params) . '&' . $progorodApiKey;

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


    /**
     * @Route("/objects/filter")
     */
    public function filterAction()
    {
        $request = $this->getRequest();
        $twigExtension = $this->get('sonata.media.twig.extension');

        try {
            $category = $request->get('category');
            if (empty($category))
                throw new \Exception('Categories is null');

            $categoryIds = explode(',', $category);

            if (empty($categoryIds))
                throw new \Exception('Categories is null');

            $categoryRepo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Category');
            $categoryTree = $categoryRepo->getDataForFilter($categoryIds);

            $filterParams = array(
                'term' => '',
                'category' => $categoryIds,
                'categoryTree' => $categoryTree,
            );

            $repo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
            $objects = $repo->filter($filterParams);

            if (! $objects)
                throw new \Exception('Not found');

            $allCategoriesIds = $repo->fetchObjectsCategories($objects);

            $rows = array();

            foreach ($objects as $obj) {

                $iconUrl = '';
                if ($obj->getIcon()) {
                    $iconUrl = $twigExtension->path($obj->getIcon(), 'reference');
                }

                $obraz = false;
                $imageUrl = '';
                if ($obj->getPrimaryCategory()) {
                    if ($obj->getPrimaryCategory()->getTitle() == 'Образы России') {
                        $obraz = true;
                        $image = $obj->getPrimaryImage();
                        $imageUrl = $this->get('sonata.media.twig.extension')->path($image, 'thumbnail');
                    }
                }

                $rows[] = array(
                    'id' => $obj->getId(),
                    'title' => $obj->getTitle(),
                    'announce' => $obj->getAnnounce(),
                    'lon' => $obj->getLon(),
                    'lat' => $obj->getLat(),
                    'icon' => $iconUrl,
                    'obraz' => $obraz,
                    'imageUrl' => $imageUrl,
                );
            }

            $response = json_encode(array(
                'success' => true,
                'result' => $rows,
                'allCategoriesIds' => array_unique($allCategoriesIds),
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

    protected function getUrl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    protected function resolveAddress($geocode)
    {
        $url = "http://geocode-maps.yandex.ru/1.x/?geocode=" . urlencode($geocode);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
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
            $latLng = (string)$featureMember->GeoObject->Point->pos;
            list($lat, $lon) = explode(' ', $latLng);
            $point = array(
                'name' => (string)$featureMember->GeoObject->name,
                'description' => (string)$featureMember->GeoObject->description,
                'lat' => $lat,
                'lon' => $lon,
            );
            $res[] = $point;
        }

        return !empty($res) ? $res[0] : false;
    }


}
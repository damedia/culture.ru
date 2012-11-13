<?php

namespace Armd\AtlasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
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
use Application\Sonata\MediaBundle\Entity\Media;

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
        $entity = $this->getObjectManager()->getObject($id);
        
        if ($entity)
            return array(
                'entity' => $entity,
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
        $userObjects = $this->getObjectManager()->getUserObjects($user);

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

        return array(
            'objects' => $this->getObjectManager()->getRussiaImagesList($searchString),
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
        $currentUser = $this->get('security.context')->getToken()->getUser();
        if ($id) {
            $entity = $repo->findOneBy(array('id'=>$id, 'published'=>true));
            if ($entity)
                return array(
                    'entity' => $entity,
                    'editable' => ($entity->getCreatedBy() == $currentUser),
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
        if (! $categories)
            throw new NotFoundHttpException("Categories not found");

        $regionsRepo = $em->getRepository('ArmdAtlasBundle:Region');
        $regions = $regionsRepo->findBy(array(), array('title'=>'ASC'));
        if (! $regions)
            throw new NotFoundHttpException("Regions not found");

        return array(
            'categories' => $categories,
            'regions' => $regions,
        );
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
     * @Route("/objects/{id}/delete", requirements={"id"="\d+"})
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
                    if ($obj->getPrimaryCategory()->getId() == 74) {
                        $obraz = true;
                        $image = $obj->getPrimaryImage(); // @TODO Много запросов
                        $imageUrl = $twigExtension->path($image, 'thumbnail');
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

    /**
     * Мои объекты. Добавить/изменить объект
     *
     * @Route("/objects/add")
     */
    public function objectsAddAction()
    {
        try {
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $repoObjectStatus = $em->getRepository('ArmdAtlasBundle:ObjectStatus');
            $repoCategory = $em->getRepository('ArmdAtlasBundle:Category');
            $repoObject = $em->getRepository('ArmdAtlasBundle:Object');

            // Название объекта
            $title = trim($request->get('title'));
            if (! $title)
                throw new \Exception('Заполните название');

            // Анонс
            $announce = trim($request->get('description'));
            if (! $announce)
                throw new \Exception('Заполните анонс');

            // Автор
            $currentUser = $this->get('security.context')->getToken()->getUser();
            if (! $currentUser)
                throw new \Exception('Пользователь не найден');

            // Создаем или редактируем объект
            $objectId = (int) $request->get('id');
            if ($objectId) {
                $mode = 'edit';
                $entity = $repoObject->findOneBy(array('id' => $objectId, 'createdBy' => $currentUser));
                if (! $entity)
                    throw new \Exception('Редактируемый объект не найден');
            } else {
                $mode = 'add';
                $entity = new Object();
            }

            $entity->setTitle($title);
            $entity->setAnnounce($announce);
            $entity->setAddress($request->get('address'));
            $entity->setLon($request->get('lon'));
            $entity->setLat($request->get('lat'));
            $entity->setIsOfficial(false); // Пользовательский объект

            // Устанавливаем статус ожидания - не нужна
            $status = $repoObjectStatus->find(0);
            $entity->setStatus($status);

            // Главная категория
            $primaryCategoryId = $request->get('primary_category');
            if ($primaryCategoryId) {
                $primaryCategory = $repoCategory->find($primaryCategoryId);
                if ($primaryCategory) {
                    $entity->setPrimaryCategory($primaryCategory);
                }
            } else
                throw new \Exception('Укажите главную категорию');

            // Второстепенные категории
            if ($objectId) {
                $secondaryCategories = $entity->getSecondaryCategories();
                foreach ($secondaryCategories as $category) {
                    $entity->removeSecondaryCategory($category);
                }
            }
            $categoryIds = $request->get('category');
            if (is_array($categoryIds) && sizeof($categoryIds)) {
                foreach ($categoryIds as $id) {
                    $id = (int) $id;
                    $category = $repoCategory->find($id);
                    if ($category) {
                        $entity->addSecondaryCategory($category);
                    }
                }
            } else
                throw new \Exception('Укажите хотя бы одну второстепенную категорию');

            // Изображения
            $mediaManager = $this->get('sonata.media.manager.media');
            $mediaIds = $request->get('media');
            if (is_array($mediaIds) && sizeof($mediaIds)) {
                foreach ($mediaIds as $id) {
                    $id = (int) $id;
                    $media = $mediaManager->findOneBy(array('id' => $id));
                    if ($media) {
                        $entity->addImage($media);
                    }
                }
            }

            // Сохраняем объект
            $em->persist($entity);
            $em->flush();

            // Добавляем объект в ACL
            $aclManager = $this->get('armd_user.manager.acl');
            $aclManager->grant($currentUser, $entity, MaskBuilder::MASK_EDIT | MaskBuilder::MASK_VIEW);

            // Возвращаем созданный объект для отрисовки иконки точки
            $lon = $entity->getLon();
            $lat = $entity->getLat();
            $imageUrl = '';
            if ($entity->getPrimaryCategory()) {
                $image = $entity->getPrimaryCategory()->getIconMedia();
                $imageUrl = $this->get('sonata.media.twig.extension')->path($image, 'reference');
            }
            if ($objStatus = $entity->getStatus()) {
                $status = $objStatus->getId();
                $statusTitle = $objStatus->getActionTitle();
                $reason = $entity->getReason();
            } else {
                $status = 0;
                $statusTitle = '';
                $reason = '';
            }

            $res = array(
                'success' => true,
                'result' => array(
                    'id' => $entity->getId(),
                    'title' => $entity->getTitle(),
                    'mode' => $mode,
                    'lon' => $lon,
                    'lat' => $lat,
                    'icon' => $imageUrl,
                    'status' => $status,
                    'statusTitle' => $statusTitle,
                    'reason' => $reason,
                ),
            );
        }
        catch (\Exception $e) {
            $res = array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
        return new Response(json_encode($res));
    }

    /**
     * Мои объекты. Добавить объект на народную карту
     *
     * @Route("/object/makepublic")
     */
    public function objectMakePublicAction()
    {
        try {
            $request = $this->getRequest();
            $id = (int) $request->get('id');
            $statusId = $request->get('is_public')=='on' ? 1 : 0;
            $em = $this->getDoctrine()->getManager();
            $repoObject = $em->getRepository('ArmdAtlasBundle:Object');
            $repoObjectStatus = $em->getRepository('ArmdAtlasBundle:ObjectStatus');
            $entity = $repoObject->find($id);
            $status = $repoObjectStatus->find($statusId);
            $entity->setStatus($status);
            $em->persist($entity);
            $em->flush();
            $res = array(
                'success' => true,
                'result' => array(
                    'id' => $entity->getId(),
                    'title' => $entity->getTitle(),
                    'status' => $entity->getStatus()->getId(),
                    'statusTitle' => $entity->getStatus()->getActionTitle(),
                ),
            );
        }
        catch (\Exception $e) {
            $res = array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
        return new Response(json_encode($res));
    }

    /**
     * Мои объекты. Список моих объектов
     * Если указан id, возвращаем одну запись
     *
     * @Route("/objects/my")
     */
    public function objectsMyAction()
    {
        try {
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $currentUser = $this->get('security.context')->getToken()->getUser();
            $repo = $em->getRepository('ArmdAtlasBundle:Object');
            $objectId = (int) $request->get('id');
            if ($objectId) {
                $obj = $repo->findOneBy(array('createdBy' => $currentUser, 'id' => $objectId), array('createdBy' => 'ASC'));
                $primaryCategory = $obj->getPrimaryCategory();
                $primaryCategoryId = $primaryCategory ? $primaryCategory->getId() : 0;
                $secondaryCategoryIds = array();
                if ($secondaryCategories = $obj->getSecondaryCategories()) {
                    foreach ($secondaryCategories as $tag) {
                        $secondaryCategoryIds[] = $tag->getId();
                    }
                }
                $result = array(
                    'id' => $obj->getId(),
                    'title' => $obj->getTitle(),
                    'announce' => $obj->getAnnounce(),
                    'address' => $obj->getAddress(),
                    'primaryCategory' => $primaryCategoryId,
                    'secondaryCategory' => $secondaryCategoryIds,
                    'lon' => $obj->getLon(),
                    'lat' => $obj->getLat(),
                    'icon' => 'http://api-maps.yandex.ru/2.0.14/release/../images/a19ee1e1e845c583b3dce0038f66be2b',
                );
            } else {
                $result = array();
                $entities = $repo->findBy(array('createdBy' => $currentUser), array('updatedAt' => 'DESC'));
                foreach ($entities as $obj) {

                    $imageUrl = '';
                    if ($obj->getPrimaryCategory()) {
                        $image = $obj->getPrimaryCategory()->getIconMedia();
                        $imageUrl = $this->get('sonata.media.twig.extension')->path($image, 'reference');
                    }

                    if ($objStatus = $obj->getStatus()) {
                        $status = $objStatus->getId();
                        $statusTitle = $objStatus->getActionTitle();
                        $reason = $obj->getReason();
                    } else {
                        $status = 0;
                        $statusTitle = '';
                        $reason = '';
                    }


                    $result[] = array(
                        'id' => $obj->getId(),
                        'title' => $obj->getTitle(),
                        'lon' => $obj->getLon(),
                        'lat' => $obj->getLat(),
                        'icon' => $imageUrl,
                        'status' => $status,
                        'statusTitle' => $statusTitle,
                        'reason' => $reason,
                    );
                }
            }

            $res = array(
                'success' => true,
                'result' => $result,
            );
        }
        catch (\Exception $e) {
            $res = array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
        return new Response(json_encode($res));
    }

    /**
     * Мои объекты. Удаление объекта
     *
     * @Route("/objects/my/delete")
     */
    public function objectsMyDeleteAction()
    {
        try {
            $request = $this->getRequest();
            $entityId = (int) $request->get('id');
            if (! $entityId)
                throw new \Exception('Объект не найден');
            $currentUser = $this->container->get('security.context')->getToken()->getUser();
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('ArmdAtlasBundle:Object');
            $entity = $repo->findOneBy(array(
                'id' => $entityId,
                'createdBy' => $currentUser,
            ));
            if (! $entity)
                throw new \Exception('Объект не найден');
            $em->remove($entity);
            $em->flush();
            $result = $entityId;
            $res = array(
                'success' => true,
                'result' => $result,
            );
        }
        catch (\Exception $e) {
            $res = array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
        return new Response(json_encode($res));
    }

    /**
     * Мои объекты. Загрузка изображений для объекта
     *
     * @Route("/objects/my/upload")
     */
    public function objectsMyUploadAction()
    {
        try {
            // Читаем бинарник картинки, пишем во временный файл
            $input = fopen('php://input', 'r');
            $tempPath = tempnam(sys_get_temp_dir(), 'media');
            $tempHandler = fopen($tempPath, 'w');
            $realSize = stream_copy_to_stream($input, $tempHandler);
            fclose($input);

            // Создаем сущность Media
            $media = new Media();
            $mediaManager = $this->get('sonata.media.manager.media');
            $binaryContent = new UploadedFile($tempPath, $this->getRequest()->get('qqfile'));
            $media->setBinaryContent($binaryContent);
            $media->setContext('atlas');
            $media->setProviderName('sonata.media.provider.image');
            $mediaManager->save($media);

            // Возвращаем инфу добавленной картинки
            $res = array(
                'success' => true,
                'result' => array(
                    'id' => $media->getId(),
                    'imageUrl' => $this->get('sonata.media.twig.extension')->path($media, 'thumbnail'),
                    'realSize' => $realSize,
                ),
            );
        }
        catch (\Exception $e) {
            $res = array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
        fclose($tempHandler);
        return new Response(json_encode($res));
    }

    public function getObjectManager()
    {
        return $this->get('armd_atlas.manager.object');
    }

    /**
     * Мои объекты. Загрузка изображений для объекта
     *
     * @Route("/objects/my/upload")
     */
    public function getRegions()
    {

    }
}
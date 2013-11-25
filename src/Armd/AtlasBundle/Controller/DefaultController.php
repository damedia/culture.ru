<?php

namespace Armd\AtlasBundle\Controller;

use Armd\UserBundle\Security\AclManager;
use Sonata\MediaBundle\Entity\MediaManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\AtlasBundle\Entity\ObjectManager;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Acl\Exception\Exception;
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
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {
    const MAX_USER_OBJECTS = 10;
    const PALETTE_COLOR_HEX = '#167667';

    private $palette_color = 'palette-color-2';

    /**
     * @Route("/objects", defaults={"_format"="json"})
     */
    public function objectsAction() {
        $request = $this->getRequest();
        $categoryIds = array_keys($request->get('category'));
        $searchTerm = $request->get('q');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Object');

        $res = $repo->search(array('term' => $searchTerm, 'category' => $categoryIds));

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

        return $entities;
    }

    /**
     * @param $objects
     * @Template("ArmdAtlasBundle:Objects:sidebarIndexWidget.html.twig")
     * @return array
     */
    public function sidebarLinkedObjectsWidgetAction(array $objects) {
        return array('items' => $objects);
    }

    /**
     * @Route("/object/{id}", requirements={"id"="\d+"}, name="armd_atlas_default_object_view")
     * @Route("/object/{id}/print", requirements={"id"="\d+"}, defaults={"isPrint"=true}, name="armd_atlas_default_object_view_print")
     */
    public function objectViewAction($id, $template = null, $isPrint = false) {
        $request = $this->getRequest();
        $om = $this->getObjectManager();
        $entity = $om->getObject($id);

        if (!$entity) {
            throw new NotFoundHttpException("Page not found");
        }

        $this->getTagManager()->loadTagging($entity);

        $criteria = array(
            ObjectManager::CRITERIA_LIMIT => 5,
            ObjectManager::CRITERIA_RUSSIA_IMAGES => true,
            ObjectManager::CRITERIA_TAGS => $entity->getTags(),
            ObjectManager::CRITERIA_RANDOM => true,
            ObjectManager::CRITERIA_NOT_IDS => array($entity->getId()),
        );
        $relatedObjects = $this->get('armd_atlas.manager.object')->findObjects($criteria);

        $template = $template ? $template : 'ArmdAtlasBundle:Default:object_view.html.twig';
        $template = $isPrint ? 'ArmdAtlasBundle:Default:object_view_print.html.twig' : $template;

        return $this->render($template, array(
            'referer' => $request->headers->get('referer'),
            'entity' => $entity,
            'relatedObjects' => $relatedObjects
        ));
    }

    /**
     * @Route("/user-objects", name="armd_atlas_user_objects")
     * @Template()
     */
    public function userObjectsAction() {
        $user = $this->get('security.context')->getToken()->getUser();
        $userObjects = $this->getObjectManager()->getUserObjects($user);

        return array(
            'user' => $user,
            'userObjects' => $userObjects
        );
    }

    /**
     * @Route("russia-images", name="armd_atlas_russia_images", options={"expose"=true})
     * @Template("ArmdAtlasBundle:Objects:imagesOfRussiaIndex.html.twig")
     */
    public function russiaImagesAction() {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $thematicCategorySlug = 'thematic';
        $thematicsRoot = $em->getRepository('ArmdAtlasBundle:Category')->findOneBySlug($thematicCategorySlug);

        if (!$thematicsRoot) {
            throw new \LogicException('Cant find atlas object category slug "'.$thematicCategorySlug.'"');
        }

        $rootCategorySlug = 'type';
        $typesRoot = $em->getRepository('ArmdAtlasBundle:Category')->findOneBySlug($rootCategorySlug);

        if (!$typesRoot) {
            throw new \LogicException('Cant find atlas object category slug "'.$rootCategorySlug.'"');
        }

        $regions = $this->getObjectManager()->getRussiaImagesDistinctRegions();

        $criteria = array();
        $criteria[ObjectManager::CRITERIA_RUSSIA_IMAGES] = true;
        $countTotal = count($this->getObjectManager()->findObjects($criteria));

        return array(
            'countTotal' => $countTotal,

            'thematics' => $thematicsRoot->getChildren(),
            'types' => $typesRoot->getChildren(),
            'regions' => $regions,

            'palette_color' => $this->palette_color,
            'palette_color_hex' => DefaultController::PALETTE_COLOR_HEX,

            'regionId' => $request->get('region_id'),
            'searchQuery' => $request->get('search_query'),
            'page' => $request->get('page', 1)
        );
    }

    /**
     * @Route("/russia-images-list/{templateName}/{offset}/{limit}",
     *      name="armd_atlas_russia_images_list",
     *      options={"expose"=true},
     *      defaults={"offset"="0", "limit"="10"}
     * )
     */

    public function russiaImagesListAction($templateName, $offset = 0, $limit = 10) {
        $templates = array(
            'tile' => 'ArmdAtlasBundle:Default:russia_images_list_tile.html.twig',
            'full-list' => 'ArmdAtlasBundle:Default:russia_images_list_full.html.twig',
            'short-list' => 'ArmdAtlasBundle:Default:russia_images_list_short.html.twig',
            'special-list' => 'ArmdAtlasBundle:Default:russia_images_list_special.html.twig'
        );

        if (!isset($templates[$templateName])) {
            throw new \InvalidArgumentException('Unknown template name '.$templateName);
        }

        $request = $this->getRequest();
        $criteria = array();

        $categoryIds = $request->get('category_ids');
        if (isset($categoryIds)) {
            $criteria[ObjectManager::CRITERIA_CATEGORY_IDS_AND] = $categoryIds;
        }

        $regionId = $request->get('region_id');
        if (isset($regionId)) {
            $criteria[ObjectManager::CRITERIA_REGION_IDS_AND] = array($regionId);
        }

        $searchText = $request->get('search_text');
        if (isset($searchText)) {
            $criteria[ObjectManager::CRITERIA_SEARCH_STRING] = $searchText;
        }

        $criteria[ObjectManager::CRITERIA_RUSSIA_IMAGES] = true;
        $criteria[ObjectManager::CRITERIA_LIMIT] = $limit;
        $criteria[ObjectManager::CRITERIA_OFFSET] = $offset;

        $objects = $this->getObjectManager()->findObjects($criteria);

        return $this->render($templates[$templateName], array(
            'objects' => $objects,
            'count' => count($objects),
            'totalCount' => $this->getObjectManager()->findObjectsCount($criteria)
        ));
    }

    /**
     * @Route("/images-of-russia-indexWidget", name="armd_atlas_images_of_russia_indexWidget", options={"expose"=true})
     * @Template("ArmdAtlasBundle:Objects:imagesOfRussia_indexWidget.html.twig")
     */
    public function imagesOfRussiaIndexWidgetAction() {
        $repo = $this->getObjectRepository();

        $russianImages = array();
        $showRecommended = $repo->countRussiaImagesForMainPage('', 'recommend');
        $showNovel = $repo->countRussiaImagesForMainPage('', 'novel');

        if ($showRecommended || $showNovel) {
            $russianImages = $repo->findRussiaImagesForMainPage('', 5, 'recommend');
        }

        return array('russianImages' => $russianImages,
                     'date' => '',
                     'showRecommended' => $showRecommended,
                     'showNovel' => $showNovel);
    }

    /**
     * @Route("/images-of-russia-list", name="armd_atlas_images_of_russia_list", options={"expose"=true})
     * @Template("ArmdAtlasBundle:Objects:imagesOfRussiaList.html.twig")
     */
    public function imagesOfRussiaListAction() {
        $request = $this->getRequest();
        $criteria = array();

        //$searchText = $request->get('search_text');
        //if (isset($searchText)) {
        //    $criteria[ObjectManager::CRITERIA_SEARCH_STRING] = $searchText;
        //}

        $filter_region = $request->get('region');
        if (isset($filter_region)) {
            $criteria[ObjectManager::CRITERIA_REGION_IDS_AND] = array($filter_region);
        }

        $filter_type = $request->get('type');
        if (isset($filter_type)) {
            $criteria[ObjectManager::CRITERIA_CATEGORY_IDS_AND] = array($filter_type);
        }

        $filter_thematic = $request->get('thematic');
        if (isset($filter_thematic)) {
            $criteria[ObjectManager::CRITERIA_CATEGORY_IDS_AND] = array($filter_thematic);
        }

        $loadedIds = $request->get('loadedIds');
        if ($loadedIds) {
            $criteria[ObjectManager::CRITERIA_NOT_IDS] = array_unique($loadedIds);
        }

        $criteria[ObjectManager::CRITERIA_RUSSIA_IMAGES] = true;
        $criteria[ObjectManager::CRITERIA_LIMIT] = 18;
        $criteria[ObjectManager::CRITERIA_RANDOM] = true;

        $objects = $this->getObjectManager()->findObjects($criteria);

        return array('objects' => $objects);
    }

    /**
     * @Route("/object/balloon", name="armd_atlas_fetch_point_details", options={"expose"=true})
     * @Template("ArmdAtlasBundle:Default:object_balloon.html.twig")
     */
    public function objectBalloonAction()
    {
        $id = (int)$this->getRequest()->query->get('id');
        $repo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
        $currentUser = $this->get('security.context')->getToken()->getUser();
        if ($id) {
            $entity = $repo->findOneBy(array('id' => $id, 'published' => true));
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
     * @Route("/object/cluster", name="armd_atlas_default_clusterballoon", options={"expose"=true})
     * @Template("ArmdAtlasBundle:Default:cluster_balloon.html.twig")
     */
    public function clusterBalloonAction()
    {
        $ids = $this->getRequest()->query->get('ids');
        $repo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
        if ($ids) {
            $entities = $repo->findBy(
                array(
                    'published' => true,
                    'id' => $ids,
                )
            );

            return array(
                'entities' => $entities,
            );
        }
    }

    /**
     * @Route("/object/side", name="armd_atlas_default_objectside", options={"expose"=true})
     * @Template("ArmdAtlasBundle:Default:object_side.html.twig")
     */
    public function objectSideAction()
    {
        $id = (int)$this->getRequest()->query->get('id');
        $repo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
        if ($id) {
            $entity = $repo->findOneBy(array('id' => $id, 'published' => true));
            if ($entity)
                return array(
                    'entity' => $entity,
                );
            else
                throw new NotFoundHttpException("Page not found");
        }
    }

    /**
     * @Route("/{filterType}",
     *  name="armd_atlas_index",
     *  defaults={"filterType"="filter_culture_objects"},
     *  options={"expose"=true}
     * )
     * @Template()
     */
    public function indexAction($filterType) {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $categoriesRepository = $em->getRepository('ArmdAtlasBundle:Category');
        $regionsRepository = $em->getRepository('ArmdAtlasBundle:Region');
        $weekendsRepository = $em->getRepository('ArmdAtlasBundle:WeekDay');

        $categories = $categoriesRepository->getDataForFilter();
        if (!$categories) {
            throw new NotFoundHttpException("Categories not found");
        }

        $regions = $regionsRepository->findBy(array(), array('title' => 'ASC'));
        if (!$regions) {
            throw new NotFoundHttpException("Regions not found");
        }

        $weekends = $weekendsRepository->findBy(array(), array('sortIndex' => 'ASC'));

        if ($request->query->has('object_id')) {
            $filterType = 'filter_user_objects';
            $objectId = $request->get('object_id');
        }
        else {
            $objectId = false;
        }

        return array(
            'categories' => $categories,
            'regions' => $regions,
            'weekends' => $weekends,
            'filterType' => $filterType,
            'objectId' => $objectId
        );
    }

    /**
     * @Route("/objects/afilters/{typeTab}", name="armd_atlas_ajax_filters", options={"expose"=true})
     * @Template("ArmdAtlasBundle:Default:ajax_filter.html.twig")
     */
    public function ajaxFilterAction($typeTab) {
        $em = $this->getDoctrine()->getManager();

        switch ($typeTab) {
            case 'filter_culture_objects':
                $repo = $em->getRepository('ArmdAtlasBundle:Category');
                $categories = $repo->getDataForFilter();
                break;

            case 'filter_tourist_clusters':
                $repo = $em->getRepository('ArmdAtlasBundle:TouristCluster');
                $categories = $repo->getDataForFilter();
                break;

            default:
                throw new NotFoundHttpException("Categories not found");
        }

        return array(
            'type_tab' => $typeTab,
            'categories' => $categories
        );
    }

    /**
     * @Route("/objects/filter", defaults={"_format"="json"}, name="armd_atlas_send_filters", options={"expose"=true})
     */
    public function filterAction() {
        $request = $this->getRequest();
        $twigExtension = $this->get('sonata.media.twig.extension');
        $legalFilterTypes = array('filter_culture_objects', 'filter_tourist_clusters', 'filter_user_objects');

        try {
            $filterType = $request->get('filter_type');

            if (!in_array($filterType, $legalFilterTypes)) {
                throw new \Exception('Unknown filter type: \''.$filterType.'\'!');
            }

            $categoryRepo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Category');
            $objectRepo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');

            if (($filterType == 'filter_culture_objects') || ($filterType == 'filter_tourist_clusters')) {
                $category = $request->get('category');

                if (empty($category)) {
                    throw new \Exception('Parameter \'category\' is undefined or empty!');
                }

                $categoryIds = array_map('intval', $category);
                $categoryTree = $categoryRepo->getDataForFilter($categoryIds);

                $filterParams = array(
                    'term' => '',
                    'category' => $categoryIds,
                    'categoryTree' => $categoryTree,
                    'filter_type' => $filterType
                );

                $objects = $objectRepo->filter($filterParams);

                if (!$objects) {
                    throw new \Exception('No corresponding objects were found!');
                }

                $allCategoriesIds = $objectRepo->fetchObjectsCategories($objects);

                $rows = array();

                foreach ($objects as $object) {
                    $iconUrl = '';

                    if ($object->getIcon()) {
                        $iconUrl = $twigExtension->path($object->getIcon(), 'reference');
                    }

                    $obraz = false;
                    $imageUrl = '';
                    $sideDetails = '';

                    if ($object->getPrimaryCategory()) {
                        if ($object->getPrimaryCategory()->getId() == 74) {
                            $obraz = true;
                            $image = $object->getPrimaryImage(); // @TODO Много запросов
                            $imageUrl = $twigExtension->path($image, 'thumbnail');
                            $sideDetails = $this->renderView('ArmdAtlasBundle:Default:object_side.html.twig', array(
                                'entity' => $object,
                            ));
                        }
                    }

                    $rows[] = array(
                        'id' => $object->getId(),
                        'title' => $object->getTitle(),
                        'announce' => $object->getAnnounce(),
                        'lon' => $object->getLon(),
                        'lat' => $object->getLat(),
                        'icon' => $iconUrl,
                        'obraz' => $obraz,
                        'imageUrl' => $imageUrl,
                        'sideDetails' => $sideDetails,
                    );
                }

                $response = array(
                    'success' => true,
                    'result' => $rows,
                    'allCategoriesIds' => array_unique($allCategoriesIds),
                );
            }
            else { //it is 'filter_user_objects' filterType
                $currentUser = $this->get('security.context')->getToken()->getUser();
                $objectId = (int)$request->get('id');

                if ($objectId) {
                    $object = $objectRepo->findOneBy(array('createdBy' => $currentUser, 'id' => $objectId));

                    $primaryCategory = $object->getPrimaryCategory();
                    $primaryCategoryId = $primaryCategory ? $primaryCategory->getId() : 0;

                    $secondaryCategoryIds = array();
                    if ($secondaryCategories = $object->getSecondaryCategories()) {
                        foreach ($secondaryCategories as $tag) {
                            $secondaryCategoryIds[] = $tag->getId();
                        }
                    }

                    $result = array(
                        'id' => $object->getId(),
                        'title' => $object->getTitle(),
                        'announce' => $object->getAnnounce(),
                        'address' => $object->getAddress(),
                        'primaryCategory' => $primaryCategoryId,
                        'secondaryCategory' => $secondaryCategoryIds,
                        'lon' => $object->getLon(),
                        'lat' => $object->getLat(),
                        'icon' => 'http://api-maps.yandex.ru/2.0.14/release/../images/a19ee1e1e845c583b3dce0038f66be2b',
                    );

                    if ($object->getPrimaryImage()) {
                        $result['primaryImage'] = array(
                            'id' => $object->getPrimaryImage()->getId(),
                            'thumbUrl' => $this->get('sonata.media.twig.extension')->path($object->getPrimaryImage(), 'thumbnail')
                        );
                    }
                    else {
                        $result['primaryImage'] = false;
                    }

                    $result['images'] = array();
                    if ($object->getImages()->count()) {
                        foreach ($object->getImages() as $image) {
                            $result['images'][] = array(
                                'id' => $image->getId(),
                                'thumbUrl' => $this->get('sonata.media.twig.extension')->path($image, 'thumbnail')
                            );
                        }
                    }
                }
                else {
                    $result = array();
                    $entities = $objectRepo->findBy(array('createdBy' => $currentUser), array('updatedAt' => 'DESC'));

                    foreach ($entities as $object) {
                        $imageUrl = '';

                        if ($object->getPrimaryCategory()) {
                            $image = $object->getPrimaryCategory()->getIconMedia();
                            $imageUrl = $this->get('sonata.media.twig.extension')->path($image, 'reference');
                        }

                        if ($objStatus = $object->getStatus()) {
                            $status = $objStatus->getId();
                            $statusTitle = $objStatus->getActionTitle();
                            $reason = $object->getReason();
                        }
                        else {
                            $status = 0;
                            $statusTitle = '';
                            $reason = '';
                        }

                        $result[] = array(
                            'id' => $object->getId(),
                            'title' => $object->getTitle(),
                            'lon' => $object->getLon(),
                            'lat' => $object->getLat(),
                            'icon' => $imageUrl,
                            'status' => $status,
                            'statusTitle' => $statusTitle,
                            'reason' => $reason,
                        );
                    }
                }

                $response = array(
                    'success' => true,
                    'result' => $result,
                );
            }
        }
        catch (\Exception $e) {
            $response = array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }

        return $response;
    }

    /**
     * Мои объекты. Добавить/изменить объект
     *
     * @Route("/objects/add", defaults={"_format"="json"})
     */
    public function objectsAddAction() {
        try {
            $request = $this->getRequest();

            $author = $this->get('security.context')->getToken()->getUser();
            if (!$author) {
                throw new \Exception('Автор не опознан!');
            }

            $em = $this->getDoctrine()->getManager();
            $objectUserData = $this->verifyAndRefineAtlasObjectUserData($request);

            $objectId = (int)$request->get('id');
            if ($objectId) {
                $mode = 'edit';

                $repoObject = $em->getRepository('ArmdAtlasBundle:Object');
                $entity = $repoObject->findOneBy(array('id' => $objectId, 'createdBy' => $author));

                if (!$entity) {
                    throw new \Exception('Редактируемый объект не найден!');
                }

                $secondaryCategories = $entity->getSecondaryCategories();
                foreach ($secondaryCategories as $category) {
                    $entity->removeSecondaryCategory($category);
                }

                $regions = $entity->getRegions();
                foreach ($regions as $region) {
                    $entity->removeRegion($region);
                }

                $weekends = $entity->getWeekends();
                foreach ($weekends as $weekend) {
                    $entity->removeWeekend($weekend);
                }

                $entity->setIsOfficial(false); //it is a user object

                $repoObjectStatus = $em->getRepository('ArmdAtlasBundle:ObjectStatus');
                $entity->setStatus($repoObjectStatus->find(0)); // Устанавливаем статус ожидания - не нужна
            }
            else {
                $mode = 'add';
                $entity = new Object();
            }

            $entity->setTitle($objectUserData['title']);
            $entity->setAnnounce($objectUserData['announce']);
            $entity->setLat($objectUserData['lat']);
            $entity->setLon($objectUserData['lon']);

            $entity->setPrimaryCategory($objectUserData['primaryCategory']);
            foreach ($objectUserData['secondaryCategories'] as $category) {
                $entity->addSecondaryCategory($category);
            }

            $entity->setSiteUrl($objectUserData['siteUrl']);
            $entity->setEmail($objectUserData['email']);
            $entity->setPhone($objectUserData['phone']);
            foreach ($objectUserData['regions'] as $region) {
                $entity->addRegion($region);
            }
            $entity->setAddress($objectUserData['address']);
            $entity->setWorkTime($objectUserData['workTime']);

            foreach ($objectUserData['weekends'] as $weekend) {
                $entity->addWeekend($weekend);
            }

            foreach ($objectUserData['images'] as $image) {
                $entity->addImage($image);
            }

            $em->persist($entity);
            $em->flush();

            // Добавляем объект в ACL
            $aclManager = $this->get('armd_user.manager.acl');
            $aclManager->grant($author, $entity, MaskBuilder::MASK_EDIT | MaskBuilder::MASK_VIEW);

            // Возвращаем созданный объект для отрисовки иконки точки
            $lon = $entity->getLon();
            $lat = $entity->getLat();
            $image = $entity->getPrimaryCategory()->getIconMedia();
            $imageUrl = $this->get('sonata.media.twig.extension')->path($image, 'reference');

            if ($objStatus = $entity->getStatus()) {
                $status = $objStatus->getId();
                $statusTitle = $objStatus->getActionTitle();
                $reason = $entity->getReason();
            }
            else {
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

        return $res;
    }

    /**
     * Мои объекты. Добавить объект на народную карту
     *
     * @Route("/object/makepublic", defaults={"_format"="json"})
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

            $this->sendMail($entity);
        }
        catch (\Exception $e) {
            $res = array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }

        return $res;
    }

    /**
     * Мои объекты. Список моих объектов
     * Если указан id, возвращаем одну запись
     *
     * @Route("/objects/my", defaults={"_format"="json"}, name="armd_atlas_default_objectsmy", options={"expose"=true})
     */
    public function objectsMyAction() {
        try {
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $currentUser = $this->get('security.context')->getToken()->getUser();
            $repo = $em->getRepository('ArmdAtlasBundle:Object');

            $objectId = (int) $request->get('id');
            if ($objectId) {
                $obj = $repo->findOneBy(array('createdBy' => $currentUser, 'id' => $objectId));

                $primaryCategory = $obj->getPrimaryCategory();
                $primaryCategoryId = $primaryCategory ? $primaryCategory->getId() : 0;

                $secondaryCategoriesIds = array();
                if ($secondaryCategories = $obj->getSecondaryCategories()) {
                    foreach ($secondaryCategories as $tag) {
                        $secondaryCategoriesIds[] = $tag->getId();
                    }
                }

                $regionsIds = array();
                if ($regions = $obj->getRegions()) {
                    foreach ($regions as $tag) {
                        $regionsIds[] = $tag->getId();
                    }
                }

                $weekendsIds = array();
                if ($weekends = $obj->getWeekends()) {
                    foreach ($weekends as $tag) {
                        $weekendsIds[] = $tag->getId();
                    }
                }

                $result = array(
                    'id' => $obj->getId(),

                    'title' => $obj->getTitle(),
                    'announce' => $obj->getAnnounce(),
                    'lat' => $obj->getLat(),
                    'lon' => $obj->getLon(),

                    'primaryCategory' => $primaryCategoryId,
                    'secondaryCategories' => $secondaryCategoriesIds,

                    'siteUrl' => $obj->getSiteUrl(),
                    'email' => $obj->getEmail(),
                    'phone' => $obj->getPhone(),
                    'regions' => $regionsIds,
                    'address' => $obj->getAddress(),
                    'workTime' => $obj->getWorkTime(),
                    'weekends' => $weekendsIds,

                    'icon' => 'http://api-maps.yandex.ru/2.0.14/release/../images/a19ee1e1e845c583b3dce0038f66be2b',
                );
                if ($obj->getPrimaryImage()) {
                    $result['primaryImage'] = array(
                        'id' => $obj->getPrimaryImage()->getId(),
                        'thumbUrl' => $this->get('sonata.media.twig.extension')->path($obj->getPrimaryImage(), 'thumbnail')
                    );
                }
                else {
                    $result['primaryImage'] = false;
                }

                $result['images'] = array();
                if ($obj->getImages()->count()) {
                    foreach ($obj->getImages() as $image) {
                        $result['images'][] = array(
                            'id' => $image->getId(),
                            'thumbUrl' => $this->get('sonata.media.twig.extension')->path($image, 'thumbnail')
                        );
                    }
                }

            }
            else {
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

        return $res;
    }

    /**
     * Мои объекты. Удаление объекта
     *
     * @Route("/objects/my/delete", defaults={"_format"="json"}, name="armd_atlas_default_objectsmydelete", options={"expose"=true})
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
            $entity = $repo->findOneBy(
                array(
                    'id' => $entityId,
                    'createdBy' => $currentUser,
                )
            );
            if (!$entity)
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

        return $res;
    }

    /**
     * Мои объекты. Загрузка изображений для объекта
     *
     * @Route("/objects/my/upload", defaults={"_format"="json"}, name="armd_atlas_default_objectsmyupload", options={"expose"=true})
     */
    public function objectsMyUploadAction()
    {
        try {
            $currentUser = $this->get('security.context')->getToken()->getUser();
            if (!$currentUser) {
                throw new \Exception('Пользователь не найден');
            }

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

            $this->get('armd_user.manager.acl')->grant($currentUser, $media, MaskBuilder::MASK_OWNER);

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

        return $res;
    }

    /**
     * Мои объекты. Удаление изображений для объекта
     *
     * @Route(
     *  "/objects/my/delete-image/{mediaId}",
     *  name="armd_atlas_default_objects_my_delete_image",
     *  defaults={"_format"="json"},
     *  options={"expose"=true}
     * )
     */
    public function objectsMyDeleteImageAction($mediaId) {
        try {
            /** @var MediaManager $mediaManager */
            $mediaManager = $this->get('sonata.media.manager.media');
            $em = $this->getDoctrine()->getManager();
            $media = $em->getRepository('Application\Sonata\MediaBundle\Entity\Media')->find($mediaId);

            if (!$media) {
                throw new \InvalidArgumentException('Изображение не найдено');
            }

            if (!$this->get('security.context')->isGranted('DELETE', $media)) {
                throw new AccessDeniedException();
            }

            $mediaManager->delete($media);

            $res = array(
                'success' => true,
                'message' => 'Изображение удалено'
            );

        } catch (Exception $ex) {
            $res = array(
                'success' => false,
                'message' => $ex->getMessage()
            );
        }

        return $res;
    }

    /**
     * @Route("/nearest/{id}", requirements={"id"="\d+"})
     * @Route("/nearest/{id}/limit/{limit}", requirements={"id"="\d+", "limit"="\d+"})
     * @Template("ArmdAtlasBundle:Default:get_nearest.html.twig")
     */
    public function getNearestAction($id, $limit=10)
    {
        try {
            $om = $this->getObjectManager();
            $object = $om->getObject($id);
            $objectsDistance = $om->findNearestRussianImages($object, (int) $limit);

            $ids = array();
            foreach ($objectsDistance as $item) {
                $ids[] = $item['id'];
            }

            $objectsByIds = array();
            $entities = $om->getPublishedObjects($ids, $limit);
            foreach ($entities as $entity) {
                $objectsByIds[$entity->getId()] = $entity;
            }

            $resultObjects = array();
            foreach ($objectsDistance as $object) {
                $resultObjects[] = array(
                    'id' => $object['id'],
                    'distance' => $object['distance'],
                    'entity' => $objectsByIds[$object['id']],
                );
            }

            return array(
                'objects' => $resultObjects,
            );

        } catch (\Exception $e) {
            return array(
                'objects' => false,
            );
        }
    }

    /**
     * @Route("/related-objects/", name="armd_atlas_related_objects")
     * @Template("ArmdAtlasBundle:Default:related_objects.html.twig")
     */
    public function relatedObjectsAction() {
        $request = $this->getRequest();
        $tags = $request->get('tags', array());
        $limit = $request->get('limit');

        $criteria = array(
            ObjectManager::CRITERIA_LIMIT => $limit,
            ObjectManager::CRITERIA_HAS_SIDE_BANNER_IMAGE => true,
            ObjectManager::CRITERIA_RANDOM => true,
            ObjectManager::CRITERIA_TAGS => $tags,
        );

        $objects = $this->getObjectManager()->findObjects($criteria);

        return array('objects' => $objects);
    }

    /**
     * @Route("/sidebar-related-objects/", name="armd_sidebar_atlas_related_objects")
     * @Template("ArmdAtlasBundle:Objects:sidebar_related_objects.html.twig")
     */
    public function sidebarRelatedObjectsAction() {
        $request = $this->getRequest();
        $tags = $request->get('tags', array());
        $limit = $request->get('limit');

        $criteria = array(
            ObjectManager::CRITERIA_LIMIT => $limit,
            ObjectManager::CRITERIA_HAS_SIDE_BANNER_IMAGE => true,
            ObjectManager::CRITERIA_RANDOM => true,
            ObjectManager::CRITERIA_TAGS => $tags,
        );

        $objects = $this->getObjectManager()->findObjects($criteria);

        return array('objects' => $objects);
    }


    /**
     * @return \Armd\AtlasBundle\Entity\ObjectManager
     */
    public function getObjectManager()
    {
        return $this->get('armd_atlas.manager.object');
    }

    /**
     * @return \Armd\TagBundle\Entity\TagManager
     */
    public function getTagManager()
    {
        return $this->get('fpn_tag.tag_manager');
    }

    /**
     * Мои объекты. Загрузка изображений для объекта
     *
     * @Route("/objects/my/upload")
     */
    public function getRegions()
    {

    }

    /**
     * Atlas object from QRcodes
     *
     * @Route("/qrobject/{filename}", requirements={"filename"="[\da-z]+\.html"})
     * @Template()
     */
    public function qrobjectAction($filename)
    {
        $locale = $this->getRequest()->getLocale();
        $docPath = $_SERVER['DOCUMENT_ROOT'] . '/qrcodes/' . $filename;
        if (file_exists($docPath)) {
            $content = file_get_contents($docPath);
            $content = str_replace("/images/", "/qrcodes/images/", $content);
            return array(
                'content' => $content
            );
        } else {
            throw new NotFoundHttpException('Not found.');
        }
    }

    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    public function getItemsSitemap($action = null, $params = array())
    {
        $items = array();

        switch ($action) {
            case 'indexAction': {
                if ($objects = $this->getObjectManager()->findObjects(array())) {
                    foreach ($objects as $o) {
                        $items[] = array(
                            'loc' => $this->generateUrl('armd_atlas_default_object_view', array(
                                'id' => $o->getId()
                            )),
                            'lastmod' => $o->getUpdatedAt()
                        );
                    }
                }

                break;
            }
        }

        return $items;
    }

    public function sendMail($object) {
        $mailer = $this->get('mailer');

        $mail = new \Swift_Message();

        $mail->setFrom($this->container->getParameter('mail_from'));
        $mail->setSubject('culture.ru: Создан новый объект атласа');
        $mail->setBody($this->renderView('ArmdAtlasBundle:Mail:user_object.html.twig', array('object' => $object)), 'text/html');
        $mail->setTo($object->getCreatedBy()->getEmail());

        $mailer->send($mail);
    }

    /**
     * @param string $type values: [recommend, novel]
     * @param string $date
     * @Route("russia-images-main/{type}/{date}",
     *  name="armd_atlas_russia_images_mainpage",
     *  defaults={"date"=""},
     *  options={"expose"=true}
     * )
     * @return Response
     */
    public function mainpageWidgetAction($type = 'recommend', $date = '')
    {
        $repo = $this->getObjectRepository();

        $russianImages = array();
        $showRecommended = $repo->countRussiaImagesForMainPage($date, 'recommend');
        $showNovel = $repo->countRussiaImagesForMainPage($date, 'novel');

        if (!$this->getRequest()->isXmlHttpRequest() && $type == 'recommend' && !$showRecommended && $showNovel) {
            $type = 'novel';
        }

        if ($showRecommended || $showNovel) {
            $russianImages = $repo->findRussiaImagesForMainPage($date, 5, $type);
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            return $this->render(
                'ArmdAtlasBundle:Default:mainpageWidgetItem.html.twig',
                array(
                    'russianImages' => $russianImages,
                    'date' => $date,
                    'showRecommended' => $showRecommended,
                    'showNovel' => $showNovel,
                )
            );
        } else {
            return $this->render(
                'ArmdAtlasBundle:Default:mainpageWidget.html.twig',
                array(
                    'russianImages' => $russianImages,
                    'date' => $date,
                    'showRecommended' => $showRecommended,
                    'showNovel' => $showNovel,
                )
            );
        }
    }

    /**
     * @return \Armd\AtlasBundle\Repository\ObjectRepository
     */
    private function getObjectRepository() { //TODO: remove this method!
        return $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
    }

    private function verifyAndRefineAtlasObjectUserData(Request $request) { //TODO: Field names in Exceptions duplicate/require translations...
        $result = array();
        $em = $this->getDoctrine()->getManager();
        $categoryRepository = $em->getRepository('ArmdAtlasBundle:Category');
        $regionRepository = $em->getRepository('ArmdAtlasBundle:Region');
        $weekdayRepository = $em->getRepository('ArmdAtlasBundle:WeekDay');

        //title (required!)
        $title = trim($request->get('title')); //TODO: filter!
        if (!$title) {
            throw new \Exception('Заполните поле "Название"!');
        }
        $result['title'] = $title;

        //announce (required!)
        $announce = trim($request->get('announce')); //TODO: filter!
        if (!$announce) {
            throw new \Exception('Заполните поле "Анонс"!');
        }
        $result['announce'] = $announce;

        //latitude (NOT required!)
        $latitude = $request->get('lat'); //TODO: filter!
        $result['lat'] = $latitude;

        //longitude (NOT required!)
        $longitude = $request->get('lon'); //TODO: filter!
        $result['lon'] = $longitude;

        //main category (required!)
        $primaryCategoryId = (int)$request->get('primaryCategory');
        if (!$primaryCategoryId) {
            throw new \Exception('Укажите значение поля "Категория"!');
        }
        $primaryCategory = $categoryRepository->find($primaryCategoryId);
        if (!$primaryCategory) {
            throw new \Exception('Указанная категория (id = '.$primaryCategoryId.') не существует!');
        }
        $result['primaryCategory'] = $primaryCategory;

        //secondary categories (at least one is required!)
        $result['secondaryCategories'] = array();
        $secondaryCategoriesIds = $request->get('secondaryCategories');

        if (!is_array($secondaryCategoriesIds) || (count($secondaryCategoriesIds) == 0)) {
            throw new \Exception('Укажите хотя бы одно значение в поле "Дополнительные категории"!');
        }
        $secondaryCategoriesIds = array_map('intval', $secondaryCategoriesIds);
        foreach ($secondaryCategoriesIds as $id) {
            $category = $categoryRepository->find($id);
            if (!$category) {
                throw new \Exception('Указанная категория (id = '.$id.') не существует!');
            }
            $result['secondaryCategories'][] = $category;
        }

        //site URL (NOT required!)
        $siteUrl = trim($request->get('siteUrl')); //TODO: filter!
        $result['siteUrl'] = $siteUrl;

        //e-mail (NOT required!)
        $email = trim($request->get('email')); //TODO: filter!
        $result['email'] = $email;

        //phone (NOT required!)
        $phone = trim($request->get('phone')); //TODO: filter!
        $result['phone'] = $phone;

        //regions (NOT required!)
        $result['regions'] = array();
        $regionsIds = $request->get('regions');
        if (is_array($regionsIds) && (count($regionsIds) > 0)) {
            $regionsIds = array_map('intval', $regionsIds);
            foreach ($regionsIds as $id) {
                $region = $regionRepository->find($id);
                if (!$region) {
                    throw new \Exception('Указанный регион (id = '.$id.') не существует!');
                }
                $result['regions'][] = $region;
            }
        }

        //address (NOT required!)
        $address = trim($request->get('address')); //TODO: filter!
        $result['address'] = $address;

        //work time (NOT required!)
        $workTime = trim($request->get('workTime')); //TODO: filter!
        $result['workTime'] = $workTime;

        //weekends (NOT required!)
        $result['weekends'] = array();
        $weekendsIds = $request->get('weekends');
        if (is_array($weekendsIds) && (count($weekendsIds) > 0)) {
            $weekendsIds = array_map('intval', $weekendsIds);
            foreach ($weekendsIds as $id) {
                $weekday = $weekdayRepository->find($id);
                if (!$weekday) {
                    throw new \Exception('Указанный день недели (id = '.$id.') не существует!');
                }
                $result['weekends'][] = $weekday;
            }
        }

        //images (NOT required!)
        $result['images'] = array();
        $mediaManager = $this->get('sonata.media.manager.media');
        $mediaIds = $request->get('media');
        if (is_array($mediaIds) && sizeof($mediaIds)) {
            $mediaIds = array_map('intval', $mediaIds);
            foreach ($mediaIds as $id) {
                $image = $mediaManager->findOneBy(array('id' => $id));
                if (!$image) {
                    throw new \Exception('Указанное изображение (id = '.$id.') не существует!');
                }
                $result['images'][] = $image;
            }
        }

        return $result;
    }
}
<?php

namespace Armd\AtlasBundle\Controller;

use Armd\UserBundle\Security\AclManager;
use Sonata\MediaBundle\Entity\MediaManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\AtlasBundle\Entity\ObjectManager;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\JsonResponse;
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


class DefaultController extends Controller
{
    const MAX_USER_OBJECTS = 10;
    /**
     * @Route("/objects", defaults={"_format"="json"})
     */
    public function objectsAction()
    {
        $request = $this->getRequest();
        $categoryIds = array_keys($request->get('category'));
        $searchTerm = $request->get('q');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Object');

        $res = $repo->search(
            array(
                'term' => $searchTerm,
                'category' => $categoryIds,
            )
        );

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
     * @Route("/object/{id}", requirements={"id"="\d+"}, name="armd_atlas_default_object_view")
     * @Route("/object/{id}/print", requirements={"id"="\d+"}, defaults={"isPrint"=true}, name="armd_atlas_default_object_view_print")
     */
    public function objectViewAction($id, $template = null, $isPrint = false)
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_atlas_russia_images')
        );

        $om = $this->getObjectManager();
        $entity = $om->getObject($id);

        if (!$entity) {
            throw new NotFoundHttpException("Page not found");
        }

        $this->getTagManager()->loadTagging($entity);

        $relatedObjects = $this->get('armd_atlas.manager.object')->findObjects
        (
            array(
                ObjectManager::CRITERIA_LIMIT => 5,
                ObjectManager::CRITERIA_RUSSIA_IMAGES => true,
                ObjectManager::CRITERIA_TAGS => $entity->getTags(),
                ObjectManager::CRITERIA_RANDOM => true,
                ObjectManager::CRITERIA_NOT_IDS => array($entity->getId()),
            )
        );

        /** @var Request $request  */
//        $request = $this->get('request');
//        $request->get;

        $template = $template ? $template : 'ArmdAtlasBundle:Default:object_view.html.twig';
        $template = $isPrint ? 'ArmdAtlasBundle:Default:object_view_print.html.twig' : $template;

        return $this->render($template, array(
            'referer' => $this->getRequest()->headers->get('referer'),
            'entity' => $entity,
            'relatedObjects' => $relatedObjects
        ));
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
     * @Route("russia-images/", name="armd_atlas_russia_images", options={"expose"=true})
     * @Template("ArmdAtlasBundle:Default:russia_images.html.twig")
     */
    public function russiaImagesAction()
    {
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_atlas_russia_images')
        );

        $em = $this->getDoctrine()->getManager();

        $thematicsRoot = $em->getRepository('ArmdAtlasBundle:Category')->findOneBySlug('thematic');
        if (empty($thematicsRoot)) {
            throw new \LogicException('Cant find atlas object category slug "thematic"');
        }

        $typesRoot = $em->getRepository('ArmdAtlasBundle:Category')->findOneBySlug('type');
        if (empty($typesRoot)) {
            throw new \LogicException('Cant find atlas object category slug "type"');
        }

        $regions = $this->getObjectManager()->getRussiaImagesDistinctRegions();

        return array(
            'thematics' => $thematicsRoot->getChildren(),
            'types' => $typesRoot->getChildren(),
            'regions' => $regions,
            'regionId' => $this->getRequest()->get('region_id'),
            'searchQuery' => $this->getRequest()->get('search_query'),
//            'totalCount' => $this->getRussiaImagesCount(array(
//                'region_id' => $this->getRequest()->get('region_id'),
//                'category_ids' => $this->getRequest()->get('category_ids'),
//                'search_text' => $this->getRequest()->get('search_query')
//            ))
        );
    }

    /**
     * @Route("/russia-images-list/{templateName}/{offset}/{limit}",
     *      name="armd_atlas_russia_images_list",
     *      options={"expose"=true},
     *      defaults={"offset"="0", "limit"="10"}
     * )
     */
    public function  russiaImagesListAction($templateName, $offset = 0, $limit = 10)
    {
        $templates = array(
            'tile' => 'ArmdAtlasBundle:Default:russia_images_list_tile.html.twig',
            'full-list' => 'ArmdAtlasBundle:Default:russia_images_list_full.html.twig',
            'short-list' => 'ArmdAtlasBundle:Default:russia_images_list_short.html.twig',
            'special-list' => 'ArmdAtlasBundle:Default:russia_images_list_special.html.twig'
        );

        if (in_array($templateName, $templates)) {
            throw new \InvalidArgumentException('Unknow template name ' . $templateName);
        }

        $request = $this->getRequest();
        $criteria = array();

        $categoryIds = $request->get('category_ids');
        if (!empty($categoryIds)) {
            $criteria[ObjectManager::CRITERIA_CATEGORY_IDS_AND] = $categoryIds;
        }

        $regionId = $request->get('region_id');
        if (!empty($regionId)) {
            $criteria[ObjectManager::CRITERIA_REGION_IDS_AND] = array($regionId);
        }

        $searchText = $request->get('search_text');
        if (!empty($searchText)) {
            $criteria[ObjectManager::CRITERIA_SEARCH_STRING] = $searchText;
        }

        $criteria[ObjectManager::CRITERIA_RUSSIA_IMAGES] = true;
        $criteria[ObjectManager::CRITERIA_LIMIT] = $limit;
        $criteria[ObjectManager::CRITERIA_OFFSET] = $offset;

        $objects = $this->getObjectManager()->findObjects($criteria);

        return $this->render(
            $templates[$templateName],
            array(
                'objects' => $objects,
                'count' => count($objects),
                'totalCount' => $this->getObjectManager()->findObjectsCount($criteria)
            )
        );

    }


    /**
     * @Route("/object/balloon", name="armd_atlas_default_objectballoon", options={"expose"=true})
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
    public function indexAction($filterType)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Category');
        $request = $this->getRequest();

        $categories = $repo->getDataForFilter();
        if (!$categories)
            throw new NotFoundHttpException("Categories not found");
        $regionsRepo = $em->getRepository('ArmdAtlasBundle:Region');
        $regions = $regionsRepo->findBy(array(), array('title' => 'ASC'));
        if (!$regions)
            throw new NotFoundHttpException("Regions not found");

        if ($request->query->has('object_id')) {
            $filterType = 'filter_user_objects';
            $objectId = $request->get('object_id');
        } else {
            $objectId = false;
        }

        return array(
            'categories' => $categories,
            'regions' => $regions,
            'filterType' => $filterType,
            'objectId' => $objectId
        );
    }

    /**
     * @Route("/objects/afilters/{typeTab}", name="armd_atlas_ajax_filters", options={"expose"=true})
     * @Template("ArmdAtlasBundle:Default:ajax_filter.html.twig")
     */
    public function ajaxFilterAction($typeTab)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = array();

        switch ($typeTab) {
            case 'filter_culture_objects':
                $repo = $em->getRepository('ArmdAtlasBundle:Category');
                $categories = $repo->getDataForFilter();
                break;
            case 'filter_tourist_clusters':
                $repo = $em->getRepository('ArmdAtlasBundle:TouristCluster');
                $categories = $repo->getDataForFilter();
                break;
        }
        // if (array_ count_values($categories))
        //    throw new NotFoundHttpException("Categories not found");

        return array(
            'type_tab' => $typeTab,
            'categories' => $categories
        );
    }
    /**
     * @Route("/objects/filter", defaults={"_format"="json"}, name="armd_atlas_default_filter", options={"expose"=true})
     */
    public function filterAction()
    {
        $request = $this->getRequest();
        $twigExtension = $this->get('sonata.media.twig.extension');

        try {
            $category = $request->get('category');
            $filterType = $request->get('filter_type');
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
                'filter_type' => $filterType
            );

            $repo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
            $objects = $repo->filter($filterParams);

            if (!$objects)
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
                $sideDetails = '';
                if ($obj->getPrimaryCategory()) {
                    if ($obj->getPrimaryCategory()->getId() == 74) {
                        $obraz = true;
                        $image = $obj->getPrimaryImage(); // @TODO Много запросов
                        $imageUrl = $twigExtension->path($image, 'thumbnail');
                        $sideDetails = $this->renderView('ArmdAtlasBundle:Default:object_side.html.twig', array(
                            'entity' => $obj,
                        ));
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
                    'sideDetails' => $sideDetails,
                );
            }

            $response = array(
                'success' => true,
                'result' => $rows,
                'allCategoriesIds' => array_unique($allCategoriesIds),
            );
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
    public function objectsAddAction()
    {
        try {
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $repoObjectStatus = $em->getRepository('ArmdAtlasBundle:ObjectStatus');
            $repoCategory = $em->getRepository('ArmdAtlasBundle:Category');
            $repoObject = $em->getRepository('ArmdAtlasBundle:Object');
            $currentUser = $this->get('security.context')->getToken()->getUser();

            // user can't create lot of objects
            $objectCount = $em->createQuery('SELECT COUNT(o) FROM ArmdAtlasBundle:Object o WHERE o.createdBy = :createdBy')
                ->setParameter('createdBy', $currentUser)
                ->getSingleScalarResult();
            if ($objectCount + 1 >= self::MAX_USER_OBJECTS) {
                throw new \RuntimeException('Слишком много объектов');
            }

            // Название объекта
            $title = trim($request->get('title'));
            if (!$title)
                throw new \Exception('Заполните название');

            // Анонс
            $announce = trim($request->get('description'));
            if (!$announce)
                throw new \Exception('Заполните анонс');

            // Автор
            if (!$currentUser)
                throw new \Exception('Пользователь не найден');

            // Создаем или редактируем объект
            $objectId = (int)$request->get('id');
            if ($objectId) {
                $mode = 'edit';
                $entity = $repoObject->findOneBy(array('id' => $objectId, 'createdBy' => $currentUser));
                if (!$entity)
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
                    if ($media && !$entity->getImages()->contains($media)) {
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
     * @Route("/objects/my", defaults={"_format"="json"}, name="armd_atlas_default_objectsmy")
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
                $obj = $repo->findOneBy(
                    array('createdBy' => $currentUser, 'id' => $objectId)
                );
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
                if ($obj->getPrimaryImage()) {
                    $result['primaryImage'] = array(
                        'id' => $obj->getPrimaryImage()->getId(),
                        'thumbUrl' => $this->get('sonata.media.twig.extension')->path($obj->getPrimaryImage(), 'thumbnail')
                    );
                } else {
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

        return $res;
    }

    /**
     * Мои объекты. Удаление объекта
     *
     * @Route("/objects/my/delete", defaults={"_format"="json"})
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
     * @Route("/objects/my/upload", defaults={"_format"="json"})
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
    public function relatedObjectsAction()
    {
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

        return array(
            'objects' => $objects
        );
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
     * @param string $type
     * @Route("russia-images-main/{type}",
     *  name="armd_atlas_russia_images_mainpage",
     *  options={"expose"=true}
     * )
     * @return Response
     */
    public function mainpageWidgetAction($type = 'recommend')
    {
        $repo = $this->getObjectRepository();
        $russianImages = $repo->findRussiaImagesForMainPage(10, $type);

        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->render(
                'ArmdAtlasBundle:Default:mainpageWidgetItem.html.twig',
                array('russianImages' => $russianImages)
            );
        } else {
            return $this->render(
                'ArmdAtlasBundle:Default:mainpageWidget.html.twig',
                array('russianImages' => $russianImages)
            );
        }
    }

    /**
     * @return \Armd\AtlasBundle\Repository\ObjectRepository
     */
    private function getObjectRepository()
    {
        return $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
    }

}
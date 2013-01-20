<?php

namespace Armd\AtlasBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Armd\AtlasBundle\Manager\ObjectManager;
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
     * @Template("ArmdAtlasBundle:Default:object_view.html.twig")
     */
    public function objectViewAction($id)
    {
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_atlas_russia_images')
        );

        $om = $this->getObjectManager();
        $entity = $om->getObject($id);

        if (!$entity) {
            throw new NotFoundHttpException("Page not found");
        }

        $relatedObjects = $this->get('armd_atlas.manager.object')->findObjects
        (
            array(
                ObjectManager::CRITERIA_RANDOM => 5,
                ObjectManager::CRITERIA_RUSSIA_IMAGES => true
            )
        );

        /** @var Request $request  */
//        $request = $this->get('request');
//        $request->get;


        return array(
            'referer' => $this->getRequest()->headers->get('referer'),
            'entity' => $entity,
            'relatedObjects' => $relatedObjects
        );
    }


    /**
     * @Route("russia-images", name="armd_atlas_russia_images")
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

        $regions = $em->createQueryBuilder()
            ->select('r')
            ->from('ArmdAtlasBundle:Object', 'o')
            ->innerJoin('ArmdAtlasBundle:Region', 'r')
            ->where('o.showAtRussianImage = TRUE')
            ->andWhere('o.published = TRUE')
            ->orderBy('r.title', 'ASC')
            ->getQuery()->getResult();


        return array(
            'thematics' => $thematicsRoot->getChildren(),
            'types' => $typesRoot->getChildren(),
            'regions' => $regions,
            'regionId' => $this->getRequest()->get('region_id')
        );
    }

    /**
     * @Route("/russia-images-list/{templateName}/{offset}/{limit}", name="armd_atlas_russia_images_list", options={"expose"=true})
     */
    public function russiaImagesListAction($templateName, $offset = 0, $limit = 10)
    {
        $templates = array(
            'tile' => 'ArmdAtlasBundle:Default:russia_images_list_tile.html.twig',
            'full-list' => 'ArmdAtlasBundle:Default:russia_images_list_full.html.twig',
            'short-list' => 'ArmdAtlasBundle:Default:russia_images_list_short.html.twig'
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

//        $searchQuery = $request->get('search_query');
//        if ($request->query->has('search_query')) {
//            $criteria[ObjectManager::CRITERIA_SEARCH_STRING] = $searchQuery;
//        }

        $criteria[ObjectManager::CRITERIA_RUSSIA_IMAGES] = true;
        $criteria[ObjectManager::CRITERIA_LIMIT] = $limit;
        $criteria[ObjectManager::CRITERIA_OFFSET] = $offset;


        return $this->render(
            $templates[$templateName],
            array(
                'objects' => $this->getObjectManager()->findObjects($criteria),
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
     * @Route("/", name="armd_atlas_index")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Category');
        $categories = $repo->getDataForFilter();
        if (!$categories)
            throw new NotFoundHttpException("Categories not found");

        $regionsRepo = $em->getRepository('ArmdAtlasBundle:Region');
        $regions = $regionsRepo->findBy(array(), array('title' => 'ASC'));
        if (!$regions)
            throw new NotFoundHttpException("Regions not found");

        return array(
            'categories' => $categories,
            'regions' => $regions,
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

            // Название объекта
            $title = trim($request->get('title'));
            if (!$title)
                throw new \Exception('Заполните название');

            // Анонс
            $announce = trim($request->get('description'));
            if (!$announce)
                throw new \Exception('Заполните анонс');

            // Автор
            $currentUser = $this->get('security.context')->getToken()->getUser();
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
        }
        catch (\Exception $e) {
            $res = array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }

        return new $res;
    }

    /**
     * Мои объекты. Список моих объектов
     * Если указан id, возвращаем одну запись
     *
     * @Route("/objects/my", defaults={"_format"="json"})
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
                    array('createdBy' => $currentUser, 'id' => $objectId),
                    array('createdBy' => 'ASC')
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

        return new $res;
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
     * @Template("ArmdAtlasBundle:Default:related_objects.html.twig")
     */
    public function relatedObjectsAction(array $tags, $limit)
    {
        $objects = $this->get('armd_atlas.manager.object')->findObjects
        (
            array(
                ObjectManager::CRITERIA_RANDOM => $limit,
                ObjectManager::CRITERIA_HAS_SIDE_BANNER_IMAGE => true
            )
        );

        return array(
            'objects' => $objects
        );
    }

    /**
     * @return \Armd\AtlasBundle\Manager\ObjectManager
     */
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
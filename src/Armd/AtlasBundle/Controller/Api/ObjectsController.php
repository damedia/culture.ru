<?php

namespace Armd\AtlasBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Armd\AtlasBundle\Entity\Object;

class ObjectsController extends Controller
{
    protected function assembleObjectArray($object)
    {
        $baseUrl = $this->getRequest()->getScheme().'://'.$this->getRequest()->getHost();
        $mediaExtension = $this->get('sonata.media.twig.extension');
        $iconUrl = $object->getIcon()
                 ? $baseUrl.$mediaExtension->path($object->getIcon(), 'reference')
                 : '';
        $obraz = false;
        $imageUrl = '';
        if ($object->getPrimaryCategory()) {
            if ($object->getPrimaryCategory()->getId() == 74) {
                $obraz = true;
                $image = $object->getPrimaryImage();
                $imageUrl = $baseUrl.$mediaExtension->path($image, 'thumbnail');
            }
        }
        return array(
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'announce' => $object->getAnnounce(),
            'lon' => $object->getLon(),
            'lat' => $object->getLat(),
            'obraz' => $obraz,
            'imageUrl' => $imageUrl,
            'iconUrl' => $iconUrl,
        );
    }

    /**
     * @Route("/objects/{id}", requirements={"id"="\d+"})
     * @Method("GET")
     */
    public function getAction($id)
    {
        try {
            $objectRepo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
            $object = $objectRepo->find($id);

            if (! $object)
                throw new \Exception('Object not found');

            $result = $this->assembleObjectArray($object);

            return new Response(json_encode(array(
                'success' => true,
                'message' => 'OK',
                'result' => $result,
            )), 200, array('Content-Type'=>'application/json'));
        }
        catch (\Exception $e) {
            return new Response(json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
            )), 200, array('Content-Type'=>'application/json'));
        }
    }

    /**
     * @Route("/objects")
     * @Method("GET")
     */
    public function indexAction()
    {
        try {
            $objectRepo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
            $objects = $objectRepo->findAll();

            if (! $objects)
                throw new \Exception('Objects not found');

            $result = array();

            foreach ($objects as $obj) {
                $result[] = $this->assembleObjectArray($obj);
            }

            return new Response(json_encode(array(
                'success' => true,
                'message' => 'OK',
                'result' => $result,
            )), 200, array('Content-Type'=>'application/json'));
        }
        catch (\Exception $e) {
            return new Response(json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
            )), 200, array('Content-Type'=>'application/json'));
        }
    }

    /**
     * @Route("/objects/create")
     * @Method("POST")
     */
    public function createAction()
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
                throw new \Exception('Пользователь не авторизован');

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

            $id = 10;

            $result = array(
                'id' => $id,
            );

            return new Response(json_encode(array(
                'success' => true,
                'message' => 'Object successfully created',
                'result' => $result,
            )), 200, array('Content-Type'=>'application/json'));
        }
        catch (\Exception $e) {
            return new Response(json_encode(array(
                'success' => false,
                'message' => $e->getMessage(),
            )), 200, array('Content-Type'=>'application/json'));
        }
    }
}
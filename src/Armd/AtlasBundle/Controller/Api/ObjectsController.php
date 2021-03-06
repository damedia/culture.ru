<?php

namespace Armd\AtlasBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\File\UploadedFile,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Application\Sonata\MediaBundle\Entity\Media;
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
     * Route("/objects/{id}", requirements={"id"="\d+"}, defaults={"_format"="json"})
     * Method("GET")
     */
    public function getAction($id)
    {
        try {
            $objectRepo = $this->getDoctrine()->getRepository('ArmdAtlasBundle:Object');
            $object = $objectRepo->find($id);

            if (! $object)
                throw new \Exception('Object not found');

            $result = $this->assembleObjectArray($object);

            return array(
                'success' => true,
                'message' => 'OK',
                'result' => $result,
            );
        }
        catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    /**
     * @Route("/objects/", defaults={"_format"="json"})
     * @Route("/objects/{id}", requirements={"id"="\d+"}, defaults={"_format"="json"})
     * @Method("GET")
     */
    public function indexAction($id=null)
    {
        try {
            $request = $this->getRequest();

            $params = array();

            if ($ids = $request->get('id')) {
                $params['id'] = array_map("trim", explode(',', $ids));
            }

            if ($fields = $request->get('fields')) {
                $params['fields'] = array_map("trim", explode(',', $fields));
            }

            $objects = $this->get('armd_atlas.manager.object')->filterForApi($params);

            if (! $objects)
                throw new \Exception('Objects not found');

            if ($id) {
                $result = $objects[0];
            } else {
                $result = $objects;
            }

            return array(
                'success' => true,
                'message' => 'OK',
                'result' => $result,
            );
        }
        catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    /**
     * @Route("/objects/create", defaults={"_format"="json"})
     * @Method("POST")
     */
    public function createAction()
    {
        $mode = 'add';

        try {
            $request = $this->getRequest();
            $em = $this->getDoctrine()->getManager();
            $repoObjectStatus = $em->getRepository('ArmdAtlasBundle:ObjectStatus');
            $repoCategory = $em->getRepository('ArmdAtlasBundle:Category');
            $repoObject = $em->getRepository('ArmdAtlasBundle:Object');

            // Название объекта
            $title = trim($request->get('title'));
            if (! $title)
                throw new \Exception('Title is required');

            // Анонс
            $announce = trim($request->get('announce'));
            if (! $announce)
                throw new \Exception('Announce is required');

            // Автор
            $currentUser = $this->get('security.context')->getToken()->getUser();
            if (! (is_object($currentUser) || $currentUser instanceof UserInterface))
                throw new \Exception('This user does not have access to this section');

            // Создаем или редактируем объект
            $objectId = (int) $request->get('id');
            if ($objectId) {
                $mode = 'edit';
                $entity = $repoObject->findOneBy(array('id' => $objectId, 'createdBy' => $currentUser));
                if (! $entity)
                    throw new \Exception('Object not found');
            } else {
                $mode = 'add';
                $entity = new Object();
                $entity->setCreatedBy($currentUser);
                $entity->setUpdatedBy($currentUser);
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
                throw new \Exception('Category is required');

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
                throw new \Exception('Secondary tags is required');

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

            $result = array(
                'id' => $entity->getId(),
                'title' => $entity->getTitle(),
                'mode' => $mode,
                'lon' => $lon,
                'lat' => $lat,
                'icon' => $imageUrl,
                'status' => $status,
                'statusTitle' => $statusTitle,
                'reason' => $reason,
            );

            return array(
                'success' => true,
                'message' => 'Object successfully created',
                'result' => $result,
            );
        }
        catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    /**
     * @Route("/objects/upload-image/{id}", defaults={"_format"="json"})
     * @Route("/objects/upload-image", defaults={"_format"="json"})
     * @Method("POST")
     */
    public function uploadImageAction($id=null)
    {
        try {
            $uploadedFile = $this->getRequest()->files->get('file');
            if (null === $uploadedFile)
                throw new \Exception('Error uploading file');

            $mediaManager = $this->get('sonata.media.manager.media');
            $media = new Media();
            $binaryContent = new UploadedFile($uploadedFile->getPathname(), $uploadedFile->getClientOriginalName());
            $media->setBinaryContent($binaryContent);
            $media->setContext('atlas');
            $media->setProviderName('sonata.media.provider.image');
            $mediaManager->save($media);

            if ($id) {
                $em = $this->getDoctrine()->getManager();
                $repoObject = $em->getRepository('ArmdAtlasBundle:Object');
                $entity = $repoObject->find($id);

                // Права только у владельца или администратора
                $currentUser = $this->get('security.context')->getToken()->getUser();
                if (! ($entity->getCreatedBy() == $currentUser || $this->get('security.context')->isGranted('ROLE_ADMIN')))
                    throw new \Exception('This user does not have access to this section');

                // Аттачим картинку к объекту
                if ($entity) {
                    $entity->addMedia($media->getId());
                    $em->persist($entity);
                    $em->flush();
                }
            }

            return array(
                'success' => true,
                'result' => array(
                    'id' => $media->getId(),
                ),
            );
        }
        catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }

}
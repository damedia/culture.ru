<?php
namespace Armd\AtlasBundle\Controller;

use Armd\AdminHelperBundle\Controller\BaseCrudController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Application\Sonata\MediaBundle\Entity\Media;
use Armd\DCXBundle\DCX\DCXDocument;

class ObjectCrudController extends BaseCrudController
{
    public function batchActionPublish(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function ($object) {
                $object->setPublished(true);
            });
    }

    public function batchActionUnpublish(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function($object) {
                $object->setPublished(false);
            });
    }

    public function batchActionShowOnMain(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function($object) {
                $object->setShowOnMain(true);
            });
    }


    public function batchActionNotShowOnMain(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->doBatchAction($selectedModelQuery, function($object) {
                $object->setShowOnMain(false);
            });
    }

    public function craeteArticleAction($dcxId)
    {
        // the key used to lookup the template
        $templateKey = 'edit';

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->getNewInstance();
        $object = $this->fillObjectFromDcx($object, $dcxId);
        
        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $view = $form->createView();

        
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        // set the theme for the current Admin Form
        return $this->render($this->admin->getTemplate($templateKey), array(
            'action' => 'create',
            'form'   => $view,
            'object' => $object,
        ));
    }

    public function checkArticleAction()
    {
        $request = $this->getRequest();
        $dcxId = $request->request->get('name');

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('ArmdAtlasBundle:Object');
        $object = $repo->findOneByDcxId($dcxId);
        if ($object != null){
            return new JsonResponse(array('error' => 'Документа с таким DC ID уже был загружен', 'success' => false));
        }

        $dcxClient = $this->get('dcx.client');
        $DcxObj = $dcxClient->getDoc($dcxId);
        if(!$DcxObj instanceof DCXDocument){
            return new JsonResponse(array('error' => 'Документа с таким DC ID не существует', 'success' => false));
        }
        return new JsonResponse(array('success' => true, 'dcxId' => $dcxId));
    }

    private function fillObjectFromDcx($object, $dcxId){
        $dcxClient = $this->get('dcx.client');
        $DcxObj = $dcxClient->getDoc($dcxId);
        $dcxClient->sendPublicateArticle($dcxId);
        $object->setDcxId($dcxId);
        $object->setTitle($DcxObj->title);
        $object->setAnnounce($DcxObj->lead);
        $object->setContent($DcxObj->body);
        $object->setSiteUrl($DcxObj->uri);
        $object->setEmail($DcxObj->email);
        $object->setPhone($DcxObj->phone);
        $object->setAddress($DcxObj->address);
        $object->setLat($DcxObj->latitude);
        $object->setLon($DcxObj->longitude);
        $object->setWorkTime($DcxObj->schedule);
        $object->setShowAtRussianImage(true);

        $storyDocuments = $DcxObj->story_documents;
        if(count($storyDocuments) > 0){
            $em = $this->getDoctrine()->getManager();
            $primaryStoryImage = $DcxObj->getPrimaryImage();
            if($primaryStoryImage != false){
                $media = new Media();
                $binaryContentImage = $dcxClient->getImageFromArticle($primaryStoryImage->href);
                $media->setTitle($primaryStoryImage->fields_title);
                $media->setBinaryContent($binaryContentImage);
                $media->setContext('atlas');
                $media->setProviderName('sonata.media.provider.dcx');
                $em->persist($media);
                $em->flush();
                $object->setPrimaryImage($media);
            }

            $galleryImages = $DcxObj->getGaleryImages();
            $media_gallery = array();
            if($galleryImages != false){
                foreach ($galleryImages as $image) {
                    $binaryContentImage = $dcxClient->getImageFromArticle($image->href);
                    $media = new Media();
                    $media->setTitle($image->fields_title);
                    $media->setBinaryContent($binaryContentImage); 
                    $media->setContext('atlas');
                    $media->setProviderName('sonata.media.provider.dcx');
                    $em->persist($media);
                    $em->flush();
                    array_push($media_gallery, $media);
                }
            }
            $object->setImages($media_gallery);
        }

        return $object;
    }

}
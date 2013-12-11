<?php
namespace Armd\AtlasBundle\Controller;

use Armd\AdminHelperBundle\Controller\BaseCrudController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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
        $dcxClient = $this->get('dcx.client');
        $DcxObj = $dcxClient->getDoc($dcxId);

        // the key used to lookup the template
        $templateKey = 'edit';

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->getNewInstance();

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

        $this->admin->setSubject($object);

        /** @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);

        // if ($this->getRestMethod()== 'POST') {
        //     $form->bind($this->get('request'));

        //     $isFormValid = $form->isValid();

        //     // persist if the form was valid and if in preview mode the preview was approved
        //     if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
        //         $this->admin->create($object);

        //         if ($this->isXmlHttpRequest()) {
        //             return $this->renderJson(array(
        //                 'result' => 'ok',
        //                 'objectId' => $this->admin->getNormalizedIdentifier($object)
        //             ));
        //         }

        //         $this->addFlash('sonata_flash_success','flash_create_success');
        //         // redirect to edit mode
        //         return $this->redirectTo($object);
        //     }

        //     // show an error message if the form failed validation
        //     if (!$isFormValid) {
        //         if (!$this->isXmlHttpRequest()) {
        //             $this->addFlash('sonata_flash_error', 'flash_create_error');
        //         }
        //     } elseif ($this->isPreviewRequested()) {
        //         // pick the preview template if the form was valid and preview was requested
        //         $templateKey = 'preview';
        //         $this->admin->getShow();
        //     }
        // }
        // var_dump($this->isPreviewRequested());
        // die();
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
        $dcxClient = $this->get('dcx.client');
        $DcxObj = $dcxClient->getDoc($dcxId);
        if(!$DcxObj instanceof DCXDocument){
            return new JsonResponse(array('error' => 'Документа с таким DC ID не существует', 'success' => false));
        }
        return new JsonResponse(array('success' => true, 'dcxId' => $dcxId));
    }

}
<?php
namespace Armd\AtlasBundle\Controller;

use Armd\AdminHelperBundle\Controller\BaseCrudController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Admin\BaseFieldDescription;
use Sonata\AdminBundle\Util\AdminObjectAclData;

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

    // protected function getRestMethod()
    // {
    //     $request = $this->getRequest();
    //     if (Request::getHttpMethodParameterOverride() || !$request->request->has('_method')) {
    //         return $request->getMethod();
    //     }

    //     return $request->request->get('_method');
    // }

    public function addArticleAction()
    {
        $request = $this->getRequest();
        if ( $request->isMethod( 'POST' ) ) {
            $dcx_id = $request->request->get('name');
            $dcx_client = $this->get('dcx.client');
            $DcxObj = $dcx_client->getDoc($dcx_id);
            if(!$DcxObj instanceof DCXDocument){
                return new JsonResponse(array('error' => 'Документа с таким DC ID не существует', 'success' => false));
            }
            return new JsonResponse(array('success' => true));
        }
        // the key used to lookup the template
        $templateKey = 'edit';

        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $object = $this->admin->getNewInstance();

        $object->setTitle('title');

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

}
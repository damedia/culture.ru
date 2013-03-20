<?php
namespace Armd\NewsBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class NewsCrudController extends CRUDController
{
    public function batchActionShowOnMain(ProxyQueryInterface $selectedModelQuery)
    {
        if ($this->admin->isGranted('EDIT') === false) {
            throw new AccessDeniedException();
        }
        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();
        try {
            foreach ($selectedModels as $selectedModel) {

                $selectedModel->setShowOnMain(true);
                $modelManager->update($selectedModel);
            }

        } catch (\Exception $e) {
            $this->get('session')->setFlash('sonata_flash_error', $this->admin->trans('Error'));

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        $this->get('session')->setFlash('sonata_flash_success', $this->admin->trans('Success'));
        
        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));        
    }

    public function batchActionNotShowOnMain(ProxyQueryInterface $selectedModelQuery)
    {
        if ($this->admin->isGranted('EDIT') === false) {
            throw new AccessDeniedException();
        }
        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();
        try {
            foreach ($selectedModels as $selectedModel) {

                $selectedModel->setShowOnMain(false);
                $modelManager->update($selectedModel);
            }

        } catch (\Exception $e) {
            $this->get('session')->setFlash('sonata_flash_error', $this->admin->trans('Error'));

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        $this->get('session')->setFlash('sonata_flash_success', $this->admin->trans('Success'));
        
        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));        
    }
}
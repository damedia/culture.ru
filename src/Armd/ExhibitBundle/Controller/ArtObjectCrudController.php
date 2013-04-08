<?php
namespace Armd\ExhibitBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ArtObjectCrudController extends CRUDController
{
    public function batchActionPublish(ProxyQueryInterface $selectedModelQuery)
    {
        if ($this->admin->isGranted('EDIT') === false) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();

        try {
            foreach ($selectedModels as $selectedModel) {

                $selectedModel->setPublished(true);
                $modelManager->update($selectedModel);
            }

        } catch (\Exception $e) {
            $this->get('session')->setFlash('sonata_flash_error', $this->admin->trans('flash_batch_publish_error'));

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        $this->get('session')->setFlash('sonata_flash_success', $this->admin->trans('flash_batch_publish_success'));

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

    public function batchActionUnpublish(ProxyQueryInterface $selectedModelQuery)
    {
        if ($this->admin->isGranted('EDIT') === false) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();

        try {
            foreach ($selectedModels as $selectedModel) {

                $selectedModel->setPublished(false);
                $modelManager->update($selectedModel);
            }

        } catch (\Exception $e) {
            $this->get('session')->setFlash('sonata_flash_error', $this->admin->trans('flash_batch_unpublish_error'));

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        $this->get('session')->setFlash('sonata_flash_success', $this->admin->trans('flash_batch_unpublish_success'));

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}
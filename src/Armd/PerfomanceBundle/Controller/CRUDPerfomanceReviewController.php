<?php
namespace Armd\PerfomanceBundle\Controller;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Armd\PerfomanceBundle\Entity\PerfomanceReview;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;

class CRUDPerfomanceReviewController extends BaseController
{
    public function batchActionPublish(ProxyQueryInterface $selectedModelQuery)
    {
        if (false === $this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();

        try {
            foreach ($selectedModels as $selectedModel) {
                
                $selectedModel->setPublished(true);
                $modelManager->update($selectedModel);
                                
            }
            $this->get('session')->setFlash('sonata_flash_success', 'flash_batch_publish_success');
        } catch ( ModelManagerException $e ) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_batch_publish_error');
        }

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));        
    }

    public function batchActionUnpublish(ProxyQueryInterface $selectedModelQuery)
    {
        if (false === $this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();

        try {
            foreach ($selectedModels as $selectedModel) {
                
                $selectedModel->setPublished(false);
                $modelManager->update($selectedModel);
                                
            }
            $this->get('session')->setFlash('sonata_flash_success', 'flash_batch_unpublish_success');
        } catch ( ModelManagerException $e ) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_batch_unpublish_error');
        }

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));             
    }

    

}
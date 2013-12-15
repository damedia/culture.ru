<?php
namespace Armd\TvigleVideoBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TvigleVideoCrudController extends CRUDController {
    public function batchActionUpdateMetadata(ProxyQueryInterface $selectedModelQuery) {
        if ($this->admin->isGranted('EDIT') === false) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();
        $selectedModels = $selectedModelQuery->execute();
        $tvigleManager = $this->get('armd_tvigle_video.manager.tvigle_video');

        try {
            foreach ($selectedModels as $selectedModel) {
                $tvigleManager->updateVideoDataFromTvigle($selectedModel);
                $modelManager->update($selectedModel);
            }

        } catch (\Exception $e) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_batch_update_metadata_error');

            return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
        }

        $this->get('session')->setFlash('sonata_flash_success', 'flash_batch_update_metadata_success');

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }
}

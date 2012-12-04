<?php
namespace Armd\MkCommentBundle\Controller;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentPersistEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\CommentBundle\Model\CommentInterface;
use Armd\MkCommentBundle\Entity\Comment;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;

class CRUDController extends BaseController
{
    public function batchActionStatePending(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchChangeState($selectedModelQuery, CommentInterface::STATE_PENDING);
    }

    public function batchActionStateDeleted(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchChangeState($selectedModelQuery, CommentInterface::STATE_DELETED);
    }

    public function batchActionStateSpam(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchChangeState($selectedModelQuery, CommentInterface::STATE_SPAM);
    }

    public function batchActionStateProcessing(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchChangeState($selectedModelQuery, Comment::STATE_PROCESSING);
    }

    public function batchActionStateVisible(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchChangeState($selectedModelQuery, CommentInterface::STATE_VISIBLE);
    }


    /**
     * @param \Sonata\AdminBundle\Datagrid\ProxyQueryInterface $selectedModelQuery
     * @param $state integer As defined in FOS\CommentBundle\Model\CommentInterface::STATE_*
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function batchChangeState(ProxyQueryInterface $selectedModelQuery, $state)
    {
        if ($this->admin->isGranted('EDIT') === false || $this->admin->isGranted('DELETE') === false)
        {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();

        $selectedModels = $selectedModelQuery->execute();

        try {
            foreach ($selectedModels as $selectedModel) {
                $selectedModel->setState($state);
                $selectedModel->setModeratedAt(new \DateTime());
                $selectedModel->setModeratedBy($this->getUser());

                $event = new CommentPersistEvent($selectedModel);
                $this->get('event_dispatcher')->dispatch(Events::COMMENT_PRE_PERSIST, $event);

                $modelManager->update($selectedModel);
            }

        } catch (\Exception $e) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_batch_state_error');

            return new RedirectResponse($this->admin->generateUrl('list',$this->admin->getFilterParameters()));
        }

        $this->get('session')->setFlash('sonata_flash_success', 'flash_batch_state_success');

        return new RedirectResponse($this->admin->generateUrl('list',$this->admin->getFilterParameters()));
    }

    public function batchActionDelete(ProxyQueryInterface $query)
    {
        if (false === $this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $modelManager = $this->admin->getModelManager();

        $selectedModels = $query->execute();

        try {
            foreach ($selectedModels as $selectedModel) {
                $modelManager->delete($selectedModel);
            }
            $this->get('session')->setFlash('sonata_flash_success', 'flash_batch_delete_success');
        } catch ( ModelManagerException $e ) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_batch_delete_error');
        }

        return new RedirectResponse($this->admin->generateUrl('list', $this->admin->getFilterParameters()));
    }

}
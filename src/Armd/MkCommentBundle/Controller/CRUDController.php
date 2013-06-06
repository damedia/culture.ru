<?php
namespace Armd\MkCommentBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentPersistEvent;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Armd\MkCommentBundle\Entity\Comment;
use Armd\MkCommentbundle\Entity\Notice;

class CRUDController extends BaseController
{
    public function batchActionStatePending(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchChangeState($selectedModelQuery, Comment::STATE_PENDING);
    }

    public function batchActionStateDeleted(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchChangeState($selectedModelQuery, Comment::STATE_DELETED);
    }

    public function batchActionStateSpam(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchChangeState($selectedModelQuery, Comment::STATE_SPAM);
    }

    public function batchActionStateProcessing(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchChangeState($selectedModelQuery, Comment::STATE_PROCESSING);
    }

    public function batchActionStateVisible(ProxyQueryInterface $selectedModelQuery)
    {
        return $this->batchChangeState($selectedModelQuery, Comment::STATE_VISIBLE);
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
                $isNotice = $state === Comment::STATE_VISIBLE
                            && ($selectedModel->getState() === Comment::STATE_PENDING 
                            || $selectedModel->getState() === Comment::STATE_PROCESSING);

                $selectedModel->setState($state);
                $selectedModel->setModeratedAt(new \DateTime());
                $selectedModel->setModeratedBy($this->getUser());

                $event = new CommentPersistEvent($selectedModel);
                $this->get('event_dispatcher')->dispatch(Events::COMMENT_PRE_PERSIST, $event);

                $modelManager->update($selectedModel);
                
                if($isNotice) {
                    $this->checkNoticeOnStateChange($selectedModel);
                }
            }
            $this->get('session')->setFlash('sonata_flash_success', 'flash_batch_state_success');
        } catch (\Exception $e) {
            $this->get('session')->setFlash('sonata_flash_error', 'flash_batch_state_error');
        }
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

    protected function checkNoticeOnStateChange($model)
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        // check `notice_replies_to_comment`
        if(sizeof($ancestors = $model->getAncestors()) && ($parentId = array_pop($ancestors))){
            $parent = $em->getRepository('\Armd\MkCommentBundle\Entity\Comment')->find($parentId);
            if($parent->getAuthor() !== $model->getAuthor() && $parent->getAuthor()->getNoticeOnComment() === Notice::T_REPLY){
                $em->persist($this->createNotice(Notice::T_REPLY, $model, $parent->getAuthor()));
            }
        }
        
        // check `notice_replies_to_threads`
        $qb2 = $em->getRepository('\Armd\MkCommentBundle\Entity\Comment')->createQueryBuilder('c');
        $qb2->select('IDENTITY(c.author)')->distinct()
            ->where('c.thread = :tid')
            ->andWhere('c.state = :state')
            ->andWhere('c.author <> :uid');
        $qb = $em->getRepository('\Armd\UserBundle\Entity\User')->createQueryBuilder('u');
        $qb->select('u')
            ->where($qb->expr()->in('u.id', $qb2->getDQL()))
            ->andWhere('u.enabled = true')
            ->andWhere('u.noticeOnComment = :ctype')
            ->setParameter('tid', $model->getThread()->getId())
            ->setParameter('state', Comment::STATE_VISIBLE)
            ->setParameter('ctype', Notice::T_THREAD)
            ->setParameter('uid', $model->getAuthor()->getId());
        $users = $qb->getQuery()->getResult();
        foreach($users as $user){
            if($user->getId() !== $model->getAuthor()->getId()){
                $em->persist($this->createNotice(Notice::T_THREAD, $model, $user));
            }
        }
        
        // check `notice_all_new_comments`
        /* Should be much faster if rewrite in native 'INSERT INTO comment_notice SELECT ... FROM fos_user_user WHERE ... ' */
        $users = $em->getRepository('\Armd\UserBundle\Entity\User')->findBy(array('noticeOnComment' => Notice::T_ALL));
        foreach($users as $user){
            if($user->getId() !== $model->getAuthor()->getId()){
                $em->persist($this->createNotice(Notice::T_ALL, $model, $user));
            }
        }
        
        $em->flush();
    }
    
    protected function createNotice($type, $comment, $owner)
    {
        $notice = new Notice();
        $notice->setUser($owner);
        $notice->setType($type);
        $notice->setCreatedAt(new \DateTime());
        $notice->setComment($comment);
        return $notice;
    }
}
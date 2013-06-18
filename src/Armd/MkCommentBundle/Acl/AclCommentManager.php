<?php

namespace Armd\MkCommentBundle\Acl;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use FOS\CommentBundle\Acl\AclCommentManager as BaseManager;

class AclCommentManager extends BaseManager
{
   
    /**
     * {@inheritDoc}
     */
    public function findCommentsByUser(UserInterface $user)
    {
        $comments = $this->realManager->findCommentsByUser($user);

        foreach ($comments as $comment) {
            if (!$this->commentAcl->canView($comment)) {
                throw new AccessDeniedException();
            }
        }

        return $comments;
    }
    
    /**
     * {@inheritDoc}
     */
    public function findNoticesForUser(UserInterface $user)
    {
        $notices = $this->realManager->findNoticesForUser($user);

        foreach ($notices as $notice) {
            if (!$this->commentAcl->canView($notice->getComment())) {
                throw new AccessDeniedException();
            }
        }

        return $notices;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getThreadCrumbsByComment($comment)
    {
        return $this->realManager->getThreadCrumbsByComment($comment);
    }
    
    /**
     * {@inheritDoc}
     */
    public function deleteUserNotices(UserInterface $user)
    {
        return $this->realManager->deleteUserNotices($user);
    }
}

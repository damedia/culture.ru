<?php

namespace Armd\CommentBundle\Twig;

use FOS\CommentBundle\Twig\CommentExtension as BaseCommentExtension;

use FOS\CommentBundle\Acl\CommentAclInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Acl\ThreadAclInterface;
use FOS\CommentBundle\Acl\VoteAclInterface;
use Armd\CommentBundle\Model\VotableObjectInterface;

use Symfony\Component\Security\Core\SecurityContextInterface;

class CommentExtension extends BaseCommentExtension
{
    protected $securityContext;

    public function __construct(CommentAclInterface $commentAcl = null,
                                VoteAclInterface $voteAcl = null,
                                ThreadAclInterface $threadAcl = null,
                                SecurityContextInterface $securityContext)
    {
        $this->commentAcl = $commentAcl;
        $this->voteAcl    = $voteAcl;
        $this->threadAcl  = $threadAcl;
        $this->securityContext = $securityContext;
    }

    public function getFunctions()
    {
        return array(
            'fos_comment_can_comment'        => new \Twig_Function_Method($this, 'canComment'),
            'fos_comment_can_vote'           => new \Twig_Function_Method($this, 'canVote'),
            'fos_comment_can_delete_comment' => new \Twig_Function_Method($this, 'canDeleteComment'),
            'fos_comment_can_edit_comment'   => new \Twig_Function_Method($this, 'canEditComment'),
            'fos_comment_can_edit_thread'    => new \Twig_Function_Method($this, 'canEditThread'),
            'fos_comment_can_comment_thread' => new \Twig_Function_Method($this, 'canCommentThread'),
            'fos_comment_can_object_vote'    => new \Twig_Function_Method($this, 'canObjectVote'),
        );
    }

    /**
     * Checks if the comment is Votable and that the user has
     * permission to vote.
     *
     * @param \FOS\CommentBundle\Model\CommentInterface $comment
     * @return bool
     */
    public function canVote(CommentInterface $comment)
    {
        if (!$comment instanceof VotableCommentInterface) {
            return false;
        }

        if ($comment->getAuthor() == $this->securityContext->getToken()->getUser()) {
            return false;
        }

        if (null === $this->voteAcl) {
            return true;
        }

        if (null !== $this->commentAcl && !$this->commentAcl->canView($comment)) {
            return false;
        }

        return $this->voteAcl->canCreate();
    }

    /**
     * @param \Armd\CommentBundle\Model\VotableObjectInterface $votableObject
     * @return bool
     */
    public function canObjectVote(VotableObjectInterface $votableObject)
    {
        if ($votableObject->getAuthor() == $this->securityContext->getToken()->getUser()) {
            return false;
        }

        return $this->securityContext->isGranted('CREATE');
    }
}
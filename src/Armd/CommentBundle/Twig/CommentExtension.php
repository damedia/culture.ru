<?php

namespace Armd\CommentBundle\Twig;

use FOS\CommentBundle\Twig\CommentExtension as BaseCommentExtension;

use FOS\CommentBundle\Acl\CommentAclInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Acl\ThreadAclInterface;
use FOS\CommentBundle\Acl\VoteAclInterface;

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

        foreach ($comment->getVotes() as $vote) {
            if ($vote->getVoter() == $this->securityContext->getToken()->getUser()) {
                return false;
            }
        }

        if (null === $this->voteAcl) {
            return true;
        }

        if (null !== $this->commentAcl && !$this->commentAcl->canView($comment)) {
            return false;
        }

        return $this->voteAcl->canCreate();
    }
}
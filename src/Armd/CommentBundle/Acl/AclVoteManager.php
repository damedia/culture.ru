<?php

namespace Armd\CommentBundle\Acl;

use FOS\CommentBundle\Acl\AclVoteManager as BaseAclVoteManager;
use FOS\CommentBundle\Model\VoteManagerInterface;
use FOS\CommentBundle\Acl\VoteAclInterface;
use FOS\CommentBundle\Acl\CommentAclInterface;
use FOS\CommentBundle\Model\VoteInterface;

class AclVoteManager extends BaseAclVoteManager
{
    /**
     * The VoteManager instance to be wrapped with ACL.
     *
     * @var VoteManagerInterface
     */
    protected $voteManager;

    /**
     * {@inheritDoc}
     */
    public function __construct(VoteManagerInterface $voteManager, VoteAclInterface $voteAcl, CommentAclInterface $commentAcl)
    {
         parent::__construct($voteManager, $voteAcl, $commentAcl);

        $this->voteManager = $voteManager;
    }

    /**
     * @param \FOS\CommentBundle\Model\VoteInterface $vote
     */
    public function removeVote(VoteInterface $vote)
    {
        $this->voteManager->removeVote($vote);
    }
}

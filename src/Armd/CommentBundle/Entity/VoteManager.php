<?php

namespace Armd\CommentBundle\Entity;

use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Entity\VoteManager as BaseVoteManager;

class VoteManager extends BaseVoteManager
{
    /**
     * @param \FOS\CommentBundle\Model\VoteInterface $vote
     */
    public function removeVote(VoteInterface $vote)
    {
        $this->em->remove($vote);
        $this->em->flush();
    }
}

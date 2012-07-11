<?php

namespace Armd\CommentBundle\EventListener;

use FOS\CommentBundle\Event\VoteEvent;
use FOS\CommentBundle\EventListener\CommentVoteScoreListener as BaseCommentVoteScoreListener;

use Armd\CommentBundle\Model\CountVotesInterface;

class CommentVoteScoreListener extends BaseCommentVoteScoreListener
{
    public function onVotePersist(VoteEvent $event)
    {
        $vote = $event->getVote();
        $comment = $vote->getComment();
        $comment->incrementScore($vote->getValue());

        if ($comment instanceof CountVotesInterface) {
            $comment->incrementCountVotes();
        }
    }
}

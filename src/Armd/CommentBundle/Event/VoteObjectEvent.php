<?php

namespace Armd\CommentBundle\Event;

use Armd\CommentBundle\Entity\VoteObject;
use Symfony\Component\EventDispatcher\Event;

class VoteObjectEvent extends Event
{
    private $vote;

    /**
     * Constructs an event.
     *
     * @param Armd\CommentBundle\Entity\VoteObject $vote
     */
    public function __construct(VoteObject $vote)
    {
        $this->vote = $vote;
    }

    /**
     * Returns the vote for the event.
     *
     * @return Armd\CommentBundle\Entity\VoteObject
     */
    public function getVote()
    {
        return $this->vote;
    }
}
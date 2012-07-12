<?php

namespace Armd\CommentBundle\Model;

interface CountVotesInterface
{
    /**
     * @param integer $countVotes
     */
    public function setCountVotes($countVotes);

    /**
     * Increments the count votes
     * value.
     *
     * @return integer The count votes
     */
    public function incrementCountVotes();

    /**
     * Derements the count votes
     * value.
     *
     * @return integer The count votes
     */
    public function decrementCountVotes();
}
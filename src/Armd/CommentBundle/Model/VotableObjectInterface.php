<?php

namespace Armd\CommentBundle\Model;

use Armd\CommentBundle\Entity\VoteObjectThread;

use Symfony\Component\Security\Core\User\UserInterface;

interface VotableObjectInterface
{
    /**
     * Sets the score of the comment.
     *
     * @param integer $score
     */
    function setScore($score);

    /**
     * Returns the current score of the comment.
     *
     * @return integer
     */
    function getScore();

    /**
     * Increments the comment score by the provided
     * value.
     *
     * @param integer by
     * @return integer The new comment score
     */
    function incrementScore($by = 1);

    /**
     * @abstract
     * @param \Armd\CommentBundle\Entity\VoteObjectThread|null $thread
     */
    function setVoteObjectThread(VoteObjectThread $thread = null);

    /**
     * Get thread
     *
     * @return \Armd\CommentBundle\Entity\VoteObjectThread
     */
    public function getVoteObjectThread();

    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getAuthor();

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $author
     */
    public function setAuthor(UserInterface $author);
}
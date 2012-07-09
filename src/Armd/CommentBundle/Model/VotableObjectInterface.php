<?php

namespace Armd\CommentBundle\Model;

use Armd\CommentBundle\Entity\VoteObjectThread;

use Symfony\Component\Security\Core\User\UserInterface;

interface VotableObjectInterface
{
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
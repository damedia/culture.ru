<?php

namespace Armd\CommentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Armd\CommentBundle\Entity\VoteObject;
use Armd\CommentBundle\Event\VoteObjectEvent;

class ObjectVoteScoreListener implements EventSubscriberInterface
{
    /**
     * @var Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    /**
     * Constructor.
     *
     * @param Symfony\Component\Security\Core\SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext = null)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param \Armd\CommentBundle\Event\VoteObjectEvent $event
     */
    public function onVoteObjectPersist(VoteObjectEvent $event)
    {
        $vote = $event->getVote();
        $thread = $vote->getThread();
        $thread->incrementScore($vote->getValue());
        $thread->incrementCountVotes();

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $vote->setVoter($this->securityContext->getToken()->getUser());
        }
    }

    static public function getSubscribedEvents()
    {
        return array('armd_comment.vote_object.pre_persist' => 'onVoteObjectPersist');
    }
}

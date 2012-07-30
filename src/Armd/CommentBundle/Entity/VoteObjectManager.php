<?php

namespace Armd\CommentBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Model\VoteInterface;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Armd\CommentBundle\Entity\VoteObject;
use Armd\CommentBundle\Event\VoteObjectEvent;
use Armd\CommentBundle\Model\VotableObjectInterface;

class VoteObjectManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \Doctrine\ORM\EntityManager $em
     * @param $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        $this->dispatcher = $dispatcher;

        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Finds a vote by id.
     *
     * @param  $id
     * @return VoteInterface
     */
    public function findVoteById($id)
    {
        return $this->findVoteBy(array('id' => $id));
    }

    /**
     * Finds a vote by specified criteria.
     *
     * @param array $criteria
     * @return VoteInterface
     */
    public function findVoteBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Finds all votes belonging to a comment.
     *
     * @param \Armd\CommentBundle\Entity\VoteObjectThread $thread
     * @return array|null
     */
    public function findObjectVotesByThread(VoteObjectThread $thread)
    {
        $qb = $this->repository->createQueryBuilder('v');
        $qb->join('v.thread', 't');
        $qb->andWhere('t.id = :threadId');
        $qb->setParameter('threadId', $thread->getId());

        $votes = $qb->getQuery()->execute();

        return $votes;
    }

    /**
     * Creates a Vote object.
     *
     * @param \Armd\CommentBundle\Entity\VoteObjectThread $thread
     * @return VoteInterface
     */
    public function createVote(VoteObjectThread $thread)
    {
        $class = $this->getClass();
        $vote = new $class();
        $vote->setThread($thread);

        $event = new VoteObjectEvent($vote);

        return $vote;
    }

    public function saveVote(VoteObject $vote)
    {
        if (null === $vote->getThread()) {
            throw new \InvalidArgumentException('Vote passed into saveVote must have a thread');
        }

        $event = new VoteObjectEvent($vote);
        $this->dispatcher->dispatch('armd_comment.vote_object.pre_persist', $event);

        $this->doSaveVote($vote);

        $event = new VoteObjectEvent($vote);
        $this->dispatcher->dispatch('armd_comment.vote_object.pre_persist', $event);
    }

    /**
     * @param Armd\CommentBundle\Entity\VoteObject $vote
     */
    public function removeVote(VoteObject $vote)
    {
        $this->em->remove($vote);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified comment vote class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Persists a vote.
     *
     * @param \Armd\CommentBundle\Entity\VoteObject $vote
     */
    protected function doSaveVote(VoteObject $vote)
    {
        $this->em->persist($vote->getThread());
        $this->em->persist($vote);
        $this->em->flush();
    }
}
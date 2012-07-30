<?php

namespace Armd\CommentBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class VoteObjectThreadManager
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
     * @param string $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Finds one comment thread by the given criteria
     *
     * @param array $criteria
     * @return VoteObjectThread
     */
    public function findThreadBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findThreadsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Finds all threads.
     *
     * @return array of VoteObjectThread
     */
    public function findAllThreads()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function isNewThread(VoteObjectThread $thread)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($thread);
    }

    /**
     * Saves a thread
     *
     * @param VoteObjectThread $thread
     */
    protected function doSaveThread(VoteObjectThread $thread)
    {
        $this->em->persist($thread);
        $this->em->flush();
    }

    /**
     * Returns the fully qualified comment thread class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $id
     * @return VoteObjectThread
     */
    public function findThreadById($id)
    {
        return $this->findThreadBy(array('id' => $id));
    }

    /**
     * Creates an empty comment thread instance
     *
     * @return Thread
     */
    public function createThread()
    {
        $class = $this->getClass();
        $thread = new $class;

        return $thread;
    }

    /**
     * Persists a thread.
     *
     * @param VoteObjectThread $thread
     */
    public function saveThread(VoteObjectThread $thread)
    {
        $this->doSaveThread($thread);
    }
}
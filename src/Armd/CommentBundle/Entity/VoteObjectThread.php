<?php

namespace Armd\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use FOS\CommentBundle\Entity\Vote as BaseVote;
use FOS\CommentBundle\Model\SignedVoteInterface;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\ThreadInterface;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(name="armd_comment_vote_object_thread")
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class VoteObjectThread
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Tells if new vote can be added in this thread
     *
     * @ORM\Column(type="boolean")
     * @var bool
     */
    protected $isVoteable = true;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isVoteable
     *
     * @param boolean $isVoteable
     * @return VoteObjectThread
     */
    public function setIsVoteable($isVoteable)
    {
        $this->isVoteable = $isVoteable;
        return $this;
    }

    /**
     * Get isVoteable
     *
     * @return boolean 
     */
    public function getIsVoteable()
    {
        return $this->isVoteable;
    }
}
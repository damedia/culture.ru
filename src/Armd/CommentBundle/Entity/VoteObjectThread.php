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
     * Comment voting score.
     *
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $score = 0;

    /**
     * Count votes.
     *
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $countVotes = 0;

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


    /**
     * Sets the current comment score.
     *
     * @param integer $score
     */
    public function setScore($score) {
        $this->score = intval($score);
    }

    /**
     * Increments the comment score by the provided
     * value.
     *
     * @param integer by
     * @return integer The new comment score
     */
    public function incrementScore($by = 1) {
        $score = $this->getScore() + intval($by);
        $this->setScore($score);
        return $score;
    }

    /**
     * Gets the current comment score.
     *
     * @return integer
     */
    public function getScore() {
        return $this->score;
    }

    /**
     * Sets the current comment score.
     *
     * @param integer $countVotes
     */
    public function setCountVotes($countVotes) {
        $this->countVotes = intval($countVotes);
    }

    /**
     * Increments the count votes
     * value.
     *
     * @return integer The new comment score
     */
    public function incrementCountVotes() {
        $countVotes = $this->getCountVotes();
        $this->setScore($countVotes++);
        return $countVotes;
    }

    /**
     * Derements the count votes
     * value.
     *
     * @return integer The new comment score
     */
    public function decrementCountVotes() {
        $countVotes = $this->getCountVotes();
        $this->setScore($countVotes--);
        return $countVotes;
    }

    /**
     * Gets the current comment score.
     *
     * @return integer
     */
    public function getCountVotes() {
        return $this->countVotes;
    }
}
<?php
namespace Armd\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\SignedCommentInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;

use Armd\CommentBundle\Model\CountVotesInterface;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="armd_comment_comment")
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Comment extends BaseComment implements SignedCommentInterface, VotableCommentInterface, CountVotesInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Thread of this comment
     *
     * @var Thread
     * @ORM\ManyToOne(targetEntity="Thread")
     */
    protected $thread;

    /**
     * Author of the comment
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @var UserInterface
     */
    protected $author;

    /**
     * Comment voting score.
     *
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $score = 0;

    /**
     * @ORM\OneToMany(targetEntity="Vote", mappedBy="comment")
     */
    protected $votes;

    /**
     * Count votes.
     *
     * @ORM\Column(type="integer")
     * @var integer
     */
    protected $countVotes = 0;

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $author
     */
    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return string|UserInterface
     */
    public function getAuthorName()
    {
        if (null === $this->getAuthor()) {
            return 'Anonymous';
        }

        return $this->getAuthor()->getUsername();
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
     * @param integer value
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
     * Get votes
     *
     * @return Vote[]
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param integer $countVotes
     */
    public function setCountVotes($countVotes) {
        $this->countVotes = intval($countVotes);
    }

    /**
     * Increments the count votes
     * value.
     *
     * @return integer The count votes
     */
    public function incrementCountVotes() {
        $countVotes = $this->getCountVotes();
        $this->setCountVotes(++$countVotes);
        return $countVotes;
    }

    /**
     * Derements the count votes
     * value.
     *
     * @return integer The count votes
     */
    public function decrementCountVotes() {
        $countVotes = $this->getCountVotes();
        $this->setCountVotes(--$countVotes);
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
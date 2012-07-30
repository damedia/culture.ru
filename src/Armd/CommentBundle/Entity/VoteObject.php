<?php

namespace Armd\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use FOS\CommentBundle\Model\SignedVoteInterface;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ExecutionContext;

use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(name="armd_comment_vote_object", uniqueConstraints={@UniqueConstraint(name="voter_object", columns={"voter_id", "thread_id", "value"})})
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class VoteObject
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Author of the comment
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @var UserInterface
     */
    protected $voter;

    /**
     * Thread of this comment
     *
     * @var Comment
     * @ORM\ManyToOne(targetEntity="VoteObjectThread")
     */
    protected $thread;

    /**
     * @ORM\Column(type="integer")
     */
    protected $value;

    public function __construct(VoteObjectThread $thread = null)
    {
        $this->thread = $thread;
    }

    /**
     * Return the comment unique id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer The votes value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param integer $value
     */
    public function setValue($value)
    {
        $this->value = intval($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isVoteValid(ExecutionContext $context)
    {
        if (!$this->checkValue($this->value)) {
            $message = 'A vote cannot have a 0 value';
            $propertyPath = $context->getPropertyPath() . '.value';
            $context->addViolationAtPath($propertyPath, $message);
        }
    }

    public function __toString()
    {
        return 'Vote #'.$this->getId();
    }

    /**
     * Checks if the value is an appropriate one.
     *
     * @param mixed $value
     *
     * @return boolean True, if the integer representation of the value is not null or 0.
     */
    protected function checkValue($value)
    {
        return null !== $value && intval($value);
    }

    /**
     * Gets the thread this vote belongs to.
     *
     * @return VoteObjectThread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Sets the thread this vote belongs to.
     *
     * @param VoteObjectThread $comment
     * @return void
     */
    public function setThread(VoteObjectThread $thread)
    {
        $this->thread = $thread;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $author
     */
    public function setVoter(UserInterface $voter)
    {
        $this->voter = $voter;
    }

    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getVoter()
    {
        return $this->voter;
    }
}
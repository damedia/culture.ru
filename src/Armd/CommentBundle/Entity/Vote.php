<?php

namespace Armd\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use FOS\CommentBundle\Entity\Vote as BaseVote;
use FOS\CommentBundle\Model\SignedVoteInterface;
use FOS\CommentBundle\Model\VoteInterface;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Table(name="armd_comment_vote", uniqueConstraints={@UniqueConstraint(name="voter_comment", columns={"voter_id", "comment_id", "value"})})
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Vote extends BaseVote implements SignedVoteInterface
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
     * @ORM\ManyToOne(targetEntity="Comment")
     */
    protected $comment;

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $author
     */
    public function setVoter(UserInterface $voter)
    {
        $this->voter = $voter;
    }

    public function getVoter()
    {
        return $this->voter;
    }
}
<?php
namespace Armd\MkCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\CommentBundle\Model\SignedCommentInterface;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use Armd\UserBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="Armd\MkCommentBundle\Repository\CommentRepository")
 * @ORM\Table(name="comment")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @ORM\HasLifecycleCallbacks()
 */
class Comment extends BaseComment implements SignedCommentInterface
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
     * @ORM\ManyToOne(targetEntity="Armd\MkCommentBundle\Entity\Thread")
     */
    protected $thread;

    /**
     * Author of the comment
     *
     * @ORM\ManyToOne(targetEntity="Armd\UserBundle\Entity\User")
     * @var User
     */
    protected $author;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="moderated_by_user_id", nullable=true)
     */
    protected $moderatedBy;

    /**
     * @ORM\Column(type="datetime", name="moderated_at", nullable=true)
     */
    protected $moderatedAt;


    /**
     * @ORM\PrePersist
     */
    public function setInitialState()
    {
        $roles = $this->author->getRoles();

        if(in_array('ROLE_ADMIN', $roles, true)) {
            $this->setState(self::STATE_VISIBLE);
        } else {
            $this->setState(self::STATE_PENDING);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \Armd\MkCommentBundle\Entity\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param \FOS\CommentBundle\Model\ThreadInterface $thread
     * @return void
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

    /**
     * Sets the author of the Comment
     * @param \Symfony\Component\Security\Core\User\UserInterface $author
     */
    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    /**
     * Gets the author of the Comment
     *
     * @return UserInterface
     */
    public function getAuthor()
    {
        return $this->author;
    }

    public function getAuthorName()
    {
        if (null === $this->getAuthor()) {
            $authorName = 'Anonymous';
        } else {
            $authorName = $this->getAuthor()->getFullname();
            if(strlen(trim($authorName)) === 0) {
                $authorName = $this->getAuthor()->getUsername();
            }
        }
        return $authorName;
    }

    public function getVisible()
    {
        return $this->visible;
    }

    public function setVisible($visible)
    {
        $this->visible = $visible;
    }

    public function getModeratedBy()
    {
        return $this->moderatedBy;
    }

    public function setModeratedBy(UserInterface $moderatedBy)
    {
        $this->moderatedBy = $moderatedBy;
    }

    public function getModeratedAt()
    {
        return $this->moderatedAt;
    }

    public function setModeratedAt(\DateTime $moderatedAt)
    {
        $this->moderatedAt = $moderatedAt;
    }

    public function getStateString()
    {
        $states = array(
            self::STATE_DELETED => 'STATE_DELETED',
            self::STATE_PENDING => 'STATE_PENDING',
            self::STATE_SPAM => 'STATE_SPAM',
            self::STATE_VISIBLE => 'STATE_VISIBLE',
        );

        return $states[$this->getState()];
    }
}

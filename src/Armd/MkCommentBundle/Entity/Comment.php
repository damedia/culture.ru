<?php
namespace Armd\MkCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Entity\Comment as BaseComment;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Comment extends BaseComment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Thread of this comment
     *
     * @var Thread
     * @ORM\ManyToOne(targetEntity="Armd\MkCommentBundle\Entity\Thread")
     */
    protected $thread;

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
}

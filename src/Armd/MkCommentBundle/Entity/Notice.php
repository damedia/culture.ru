<?php
namespace Armd\MkCommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\MkCommentBundle\Model\NoticeInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="comment_notice")
 */
class Notice implements NoticeInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type;
    
    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;
    
    /**
     * Comment for this notice
     *
     * @var Comment
     * @ORM\ManyToOne(targetEntity="Armd\MkCommentBundle\Entity\Comment", cascade={"persist"}, inversedBy="notices")
     */
    protected $comment;

    /**
     * Receiver of the notice
     *
     * @ORM\ManyToOne(targetEntity="Armd\UserBundle\Entity\User")
     * @var User
     */
    protected $user;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @see NoticeInterface::T_*
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @see NoticeInterface::T_*
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    
    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }
    
    /**
     * @return Comment
     */
    public function getComment()
    {
        return $this->comment;
    }
    
    /**
     * @param Comment $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
    
    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     */
    public function setUser(\Symfony\Component\Security\Core\User\UserInterface $user)
    {
        $this->user = $user;
    }
}
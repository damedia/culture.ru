<?php

namespace Armd\CommunicationPlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Armd\CommentBundle\Entity\Thread;
use Armd\CommentBundle\Entity\VoteObjectThread;
use Armd\CommentBundle\Model\VotableObjectInterface;

use Symfony\Component\Security\Core\User\UserInterface;
/**
 * Armd\CommunicationPlatformBundle\Entity\Proposals
 *
 * @ORM\Table(name="armd_cp_proposals")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Proposals implements VotableObjectInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $enabled;

    /**
     * @ORM\ManyToOne(targetEntity="Topic", inversedBy="proposals")
     * @ORM\JoinColumn(name="topic_id", referencedColumnName="id")
     */
    protected $topic;

    /**
     * Thread of this comment
     *
     * @var Armd\CommentBundle\Entity\Thread
     * @ORM\ManyToOne(targetEntity="Armd\CommentBundle\Entity\Thread", cascade={"all"}, fetch="EAGER")
     */
    protected $thread;

    /**
     * Thread of this comment
     *
     * @var Armd\CommentBundle\Entity\VoteObjectThread
     * @ORM\ManyToOne(targetEntity="Armd\CommentBundle\Entity\VoteObjectThread", cascade={"all"}, fetch="EAGER")
     */
    protected $voteObjectThread;

    /**
     * Author of the comment
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     * @var UserInterface
     */
    protected $author;

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
     * Set content
     *
     * @param text $content
     * @return Proposals
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get content
     *
     * @return text 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return Proposals
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set topic
     *
     * @param \Armd\CommunicationPlatformBundle\Entity\Topic $topic
     * @return Proposals
     */
    public function setTopic(\Armd\CommunicationPlatformBundle\Entity\Topic $topic = null)
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     * Get topic
     *
     * @return \Armd\CommunicationPlatformBundle\Entity\Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getId() ?: '-';
    }

    /**
     * Set thread
     *
     * @param Armd\CommentBundle\Entity\Thread $thread
     * @return Proposals
     */
    public function setThread(Thread $thread = null)
    {
        $this->thread = $thread;
        return $this;
    }

    /**
     * Get thread
     *
     * @return Armd\CommentBundle\Entity\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set thread
     *
     * @param Armd\CommentBundle\Entity\VoteObjectThread $thread
     * @return Proposals
     */
    public function setVoteObjectThread(VoteObjectThread $voteObjectThread = null)
    {
        $this->voteObjectThread = $voteObjectThread;
        return $this;
    }

    /**
     * Get thread
     *
     * @return Armd\CommentBundle\Entity\VoteObjectThread
     */
    public function getVoteObjectThread()
    {
        return $this->voteObjectThread;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $author
     */
    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
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
}
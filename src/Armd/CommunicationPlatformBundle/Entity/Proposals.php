<?php

namespace Armd\CommunicationPlatformBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\CommunicationPlatformBundle\Entity\Proposals
 *
 * @ORM\Table(name="cp_proposals")
 * @ORM\Entity
 */
class Proposals
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\Column(type="integer", name="count_likes")
     */
    protected $countLikes = 0;

    /**
     * @ORM\Column(type="integer", name="count_comments")
     */
    protected $countComments = 0;

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
     * Set countLikes
     *
     * @param int $countLikes
     * @return Proposals
     */
    public function setCountLikes(\int $countLikes)
    {
        $this->countLikes = $countLikes;
        return $this;
    }

    /**
     * Get countLikes
     *
     * @return integer
     */
    public function getCountLikes()
    {
        return $this->countLikes;
    }

    /**
     * Set countComments
     *
     * @param int $countComments
     * @return Proposals
     */
    public function setCountComments(\int $countComments)
    {
        $this->countComments = $countComments;
        return $this;
    }

    /**
     * Get countComments
     *
     * @return integer
     */
    public function getCountComments()
    {
        return $this->countComments;
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
}
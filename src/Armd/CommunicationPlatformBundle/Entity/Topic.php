<?php

namespace Armd\CommunicationPlatformBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\CommunicationPlatformBundle\Entity\Topic
 *
 * @ORM\Table(name="armd_cp_topic")
 * @ORM\Entity
 */
class Topic
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
     * @ORM\Column(type="string", length=100)
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     */
    protected $decription;

    /**
     * @ORM\OneToMany(targetEntity="Proposals", mappedBy="topic")
     */
    protected $proposals;

    public function __construct()
    {
        $this->proposals = new ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     * @return Topic
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set decription
     *
     * @param text $decription
     * @return Topic
     */
    public function setDecription($decription)
    {
        $this->decription = $decription;
        return $this;
    }

    /**
     * Get decription
     *
     * @return text
     */
    public function getDecription()
    {
        return $this->decription;
    }

    /**
     * Add proposals
     *
     * @param \Armd\CommunicationPlatformBundle\Entity\Proposals $proposals
     * @return Topic
     */
    public function addProposals(\Armd\CommunicationPlatformBundle\Entity\Proposals $proposals)
    {
        $this->proposals[] = $proposals;
        return $this;
    }

    /**
     * Get proposals
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProposals()
    {
        return $this->proposals;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ?: '-';
    }
}
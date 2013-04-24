<?php

namespace Armd\PerfomanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Armd\UserBundle\Entity\User;
use Armd\PerfomanceBundle\Entity\Perfomance;

/**
 * @ORM\Table(name="content_perfomance_review")
 * @ORM\Entity
 */
class PerfomanceReview 
{
    
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Armd\PerfomanceBundle\Entity\Perfomance", cascade={"persist"})
     * @ORM\JoinColumn(name="perfomance_id", referencedColumnName="id", nullable=true)
     */
    private $perfomance; 
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Armd\UserBundle\Entity\User")
     */
    protected $author;    

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published = true;
    
    /**
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;    

    private $commentCount;
       

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PerfomanceReview
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return PerfomanceReview
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return PerfomanceReview
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set perfomance
     *
     * @param \Armd\PerfomanceBundle\Entity\Perfomance $perfomance
     * @return PerfomanceReview
     */
    public function setPerfomance(\Armd\PerfomanceBundle\Entity\Perfomance $perfomance = null)
    {
        $this->perfomance = $perfomance;
    
        return $this;
    }

    /**
     * Get perfomance
     *
     * @return \Armd\PerfomanceBundle\Entity\Perfomance 
     */
    public function getPerfomance()
    {
        return $this->perfomance;
    }

    /**
     * Set author
     *
     * @param \Armd\UserBundle\Entity\User $author
     * @return PerfomanceReview
     */
    public function setAuthor(\Armd\UserBundle\Entity\User $author = null)
    {
        $this->author = $author;
    
        return $this;
    }

    /**
     * Get author
     *
     * @return \Armd\UserBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }
    
    /**
     * Set commentCount
     *
     * @return PerfomanceReview
     */
    public function setCommentCount($comment_count = null)
    {
        $this->commentCount = $comment_count;
    
        return $this;
    }  

    /**
     * Get comments
     *
     */
    public function getCommentCount()
    {
        return $this->commentCount;
    
    }        
    
    public function getPerfomanceId() {
        
        if ($this -> perfomance)
            return $this -> getPerfomance() -> getId();
         
        return null;
    }
    
    public function setPerfomanceId() {
        
        return $this;
    }    
}
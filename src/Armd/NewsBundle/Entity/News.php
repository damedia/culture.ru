<?php

namespace Armd\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Model\ThreadInterface;
use Armd\NewsBundle\Model\News as BaseNews;
use Armd\MkCommentBundle\Model\CommentableInterface;
use Armd\MkCommentBundle\Entity\Thread;

/**
 * @ORM\Entity(repositoryClass="Armd\NewsBundle\Repository\NewsRepository")
 * @ORM\Table(name="content_news") 
 * @ORM\HasLifecycleCallbacks
 */
class News extends BaseNews implements CommentableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    
    /**
     * @ORM\Column(type="datetime", name="date_from")
     */
    protected $date;
    
    /**
     * @ORM\Column(type="datetime", name="date_to", nullable=true)
     */
    protected $endDate;    

    /**
     * @ORM\Column(type="text", nullable=true)
     */    
    protected $announce;

    /**
     * @ORM\Column(type="text")
     */    
    protected $body;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */    
    protected $source;    
    
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\OrderBy({"title" = "ASC"})     
     **/
    protected $category;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */        
    protected $important;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */        
    protected $priority;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */        
    protected $published;

    /**
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */            
    protected $publishedAt;    
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $month;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $day;    
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    private $gallery;
    
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */        
    private $borodino;
    

    /**
     * Thread of this comment
     *
     * @var \Armd\MkCommentBundle\Entity\Thread
     * @ORM\ManyToOne(targetEntity="Armd\MkCommentBundle\Entity\Thread", cascade={"all"}, fetch="EAGER")
     */
    protected $thread;
    
    /** 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setDateParts()
    {
        $this->day = $this->getDate()->format('d');
        $this->month = $this->getDate()->format('m');
    }

    /** 
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */    
    public function setPublicationDate() 
    {
        if ($this->published)
        {
            $this->publishedAt = new \DateTime();
        }
    }       

    /**
     * Set announce
     *
     * @param text $announce
     * @return News
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
        return $this;
    }

    /**
     * Get announce
     *
     * @return text 
     */
    public function getAnnounce()
    {
        return $this->announce;
    }

    /**
     * Set body
     *
     * @param text $body
     * @return News
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     *
     * @return text 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set category
     *
     * @param Armd\NewsBundle\Entity\Category $category
     * @return News
     */
    public function setCategory(\Armd\NewsBundle\Entity\Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return Armd\NewsBundle\Entity\Categoty 
     */
    public function getCategory()
    {
        return $this->category;
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
     * @return News
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
     * Set important
     *
     * @param boolean $important
     * @return News
     */
    public function setImportant($important)
    {
        $this->important = $important;
        return $this;
    }

    /**
     * Get important
     *
     * @return boolean 
     */
    public function getImportant()
    {
        return $this->important;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return News
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
     * Set source
     *
     * @param string $source
     * @return News
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set image
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $image
     * @return News
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set gallery
     *
     * @param Application\Sonata\MediaBundle\Entity\Gallery $gallery
     * @return News
     */
    public function setGallery(\Application\Sonata\MediaBundle\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * Get gallery
     *
     * @return Application\Sonata\MediaBundle\Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set endDate
     *
     * @param datetime $endDate
     * @return News
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Get endDate
     *
     * @return datetime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return News
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set date
     *
     * @param datetime $date
     * @return News
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set month
     *
     * @param integer $month
     * @return News
     */
    public function setMonth($month)
    {
        $this->month = $month;
        return $this;
    }

    /**
     * Get month
     *
     * @return integer 
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set day
     *
     * @param integer $day
     * @return News
     */
    public function setDay($day)
    {
        $this->day = $day;
        return $this;
    }

    /**
     * Get day
     *
     * @return integer 
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set thread
     *
     * @param \Armd\MkCommentBundle\Entity\Thread $thread
     * @return News
     */
    public function setThread(Thread $thread = null)
    {
        $this->thread = $thread;
        return $this;
    }

    /**
     * Get thread
     *
     * @return \Armd\MkCommentBundle\Entity\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     * @return News
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;
    
        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime 
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set borodino
     *
     * @param boolean $borodino
     * @return News
     */
    public function setBorodino($borodino)
    {
        $this->borodino = $borodino;
    
        return $this;
    }

    /**
     * Get borodino
     *
     * @return boolean 
     */
    public function getBorodino()
    {
        return $this->borodino;
    }
}
<?php

namespace Armd\ChronicleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\ChronicleBundle\Entity\Event
 *
 * @ORM\Entity(repositoryClass="Armd\ChronicleBundle\Repository\EventRepository")
 * @ORM\Table(name="content_chronicle_event")
 * @ORM\HasLifecycleCallbacks 
 */
class Event
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
     * @var string $title
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var text $announce
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $announce;

    /**
     * @var text $body
     *
     * @ORM\Column(type="text")
     */
    private $body;

    /**
     * @var date $event_date
     *
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var integer $century
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $century;

    /**
     * @var integer $decade
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $decade;

    /**
     * @var integer $year
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */        
    private $published;
    
    /**    
     * @ORM\OneToMany(targetEntity="Accident", mappedBy="event")    
     */
    private $accidents;
        
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
     * @var integer $priority
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $priority;

    public function __toString()
    {
        return $this->getTitle();
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->accidents = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Event
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
     * Set announce
     *
     * @param string $announce
     * @return Event
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
    
        return $this;
    }

    /**
     * Get announce
     *
     * @return string 
     */
    public function getAnnounce()
    {
        return $this->announce;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Event
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
     * Set date
     *
     * @param \DateTime $date
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;
        
        $year = $date->format('Y');

        $this->setCentury(floor($year / 100) + 1);
        $this->setDecade(floor($year / 10));
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set century
     *
     * @param integer $century
     * @return Event
     */
    public function setCentury($century)
    {
        $this->century = $century;
    
        return $this;
    }

    /**
     * Get century
     *
     * @return integer 
     */
    public function getCentury()
    {
        return $this->century;
    }

    /**
     * Set decade
     *
     * @param integer $decade
     * @return Event
     */
    public function setDecade($decade)
    {
        $this->decade = $decade;
    
        return $this;
    }

    /**
     * Get decade
     *
     * @return integer 
     */
    public function getDecade()
    {
        return $this->decade;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Event
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return integer 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Event
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
     * Set priority
     *
     * @param integer $priority
     * @return Event
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
     * Add accidents
     *
     * @param Armd\ChronicleBundle\Entity\Accident $accidents
     * @return Event
     */
    public function addAccident(\Armd\ChronicleBundle\Entity\Accident $accidents)
    {
        $this->accidents[] = $accidents;
    
        return $this;
    }

    /**
     * Remove accidents
     *
     * @param Armd\ChronicleBundle\Entity\Accident $accidents
     */
    public function removeAccident(\Armd\ChronicleBundle\Entity\Accident $accidents)
    {
        $this->accidents->removeElement($accidents);
    }

    /**
     * Get accidents
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAccidents()
    {
        return $this->accidents;
    }

    /**
     * Set image
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $image
     * @return Event
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
     * @return Event
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
}
<?php

namespace Armd\TheaterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Application\Sonata\MediaBundle\Entity\Media;

/**
 * @ORM\Entity()
 * @ORM\Table(name="armd_theater") 
 */
class Theater
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */        
    protected $published;
    
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;  
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $director;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $address;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Url
     */
    protected $url;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Email
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $metro;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ticketOfficeMode;
    
    /**
     * @ORM\Column(type="decimal", precision=15, scale=10, nullable=true)
     */
    protected $latitude;
    
    /**
     * @ORM\Column(type="decimal", precision=15, scale=10, nullable=true)
     */
    protected $longitude;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    private $gallery;
    
    /**
     * @ORM\ManyToMany(targetEntity="Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"all"})
     * @ORM\JoinTable(name="armd_theater_interviews")
     */
    private $interviews;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="armd_theater_media_interviews")
     */
    private $mediaInterviews;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\AddressBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;

    /**
     * @ORM\ManyToMany(targetEntity="TheaterCategory")
     * @ORM\JoinTable(name="armd_theater_theater_category")
     */
    private $categories;
    
    /**
     * @ORM\OneToMany(targetEntity="Billboard", mappedBy="theater", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $billboards;
    
    /**
     * @ORM\OneToMany(targetEntity="Armd\PerfomanceBundle\Entity\Perfomance", mappedBy="theater")
     * @ORM\OrderBy({"title" = "ASC"})
     */
    private $performances;

    public function __toString()
    {
        return $this->getTitle();
    }    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
        $this->billboards = new \Doctrine\Common\Collections\ArrayCollection();
        $this->performances = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mediaInterviews = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Theater
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
     * Set description
     *
     * @param string $description
     * @return Theater
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Theater
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
     * Set url
     *
     * @param string $url
     * @return Theater
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Theater
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Theater
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    
        return $this;
    }

    /**
     * Get phone
     *
     * @return string 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return Theater
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set city
     *
     * @param \Armd\AddressBundle\Entity\City $city
     * @return Theater
     */
    public function setCity(\Armd\AddressBundle\Entity\City $city = null)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return \Armd\AddressBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set director
     *
     * @param string $director
     * @return Theater
     */
    public function setDirector($director)
    {
        $this->director = $director;
    
        return $this;
    }

    /**
     * Get director
     *
     * @return string 
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Theater
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Theater
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
     * Add categories
     *
     * @param \Armd\TheaterBundle\Entity\TheaterCategory $categories
     * @return Theater
     */
    public function addCategorie(\Armd\TheaterBundle\Entity\TheaterCategory $categories)
    {
        $this->categories[] = $categories;
    
        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Armd\TheaterBundle\Entity\TheaterCategory $categories
     */
    public function removeCategorie(\Armd\TheaterBundle\Entity\TheaterCategory $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add billboards
     *
     * @param \Armd\TheaterBundle\Entity\Billboard $billboards
     * @return Theater
     */
    public function addBillboard(\Armd\TheaterBundle\Entity\Billboard $billboards)
    {
        $this->billboards[] = $billboards;
    
        return $this;
    }

    /**
     * Remove billboards
     *
     * @param \Armd\TheaterBundle\Entity\Billboard $billboards
     */
    public function removeBillboard(\Armd\TheaterBundle\Entity\Billboard $billboards)
    {
        $this->billboards->removeElement($billboards);
    }

    /**
     * Get billboards
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBillboards()
    {
        return $this->billboards;
    }

    /**
     * Set metro
     *
     * @param string $metro
     * @return Theater
     */
    public function setMetro($metro)
    {
        $this->metro = $metro;
    
        return $this;
    }

    /**
     * Get metro
     *
     * @return string 
     */
    public function getMetro()
    {
        return $this->metro;
    }

    /**
     * Set ticketOfficeMode
     *
     * @param string $ticketOfficeMode
     * @return Theater
     */
    public function setTicketOfficeMode($ticketOfficeMode)
    {
        $this->ticketOfficeMode = $ticketOfficeMode;
    
        return $this;
    }

    /**
     * Get ticketOfficeMode
     *
     * @return string 
     */
    public function getTicketOfficeMode()
    {
        return $this->ticketOfficeMode;
    }

    /**
     * Set latitude
     *
     * @param decimal $latitude
     * @return Theater
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    
        return $this;
    }

    /**
     * Get latitude
     *
     * @return decimal 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param decimal $longitude
     * @return Theater
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    
        return $this;
    }

    /**
     * Get longitude
     *
     * @return decimal 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Add performances
     *
     * @param \Armd\PerfomanceBundle\Entity\Perfomance $performances
     * @return Theater
     */
    public function addPerformance(\Armd\PerfomanceBundle\Entity\Perfomance $performances)
    {
        $this->performances[] = $performances;
    
        return $this;
    }

    /**
     * Remove performances
     *
     * @param \Armd\PerfomanceBundle\Entity\Perfomance $performances
     */
    public function removePerformance(\Armd\PerfomanceBundle\Entity\Perfomance $performances)
    {
        $this->performances->removeElement($performances);
    }

    /**
     * Get performances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPerformances()
    {
        return $this->performances;
    }

    /**
     * Set gallery
     *
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $gallery
     * @return Theater
     */
    public function setGallery(\Application\Sonata\MediaBundle\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;
    
        return $this;
    }

    /**
     * Get gallery
     *
     * @return \Application\Sonata\MediaBundle\Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Add interviews
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $interviews
     * @return Theater
     */
    public function addInterview(\Armd\TvigleVideoBundle\Entity\TvigleVideo $interviews)
    {
        $this->interviews[] = $interviews;
    
        return $this;
    }

    /**
     * Remove interviews
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $interviews
     */
    public function removeInterview(\Armd\TvigleVideoBundle\Entity\TvigleVideo $interviews)
    {
        $this->interviews->removeElement($interviews);
    }

    /**
     * Get interviews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInterviews()
    {
        return $this->interviews;
    }

    /**
     * Add mediaInterview
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $mediaInterview
     * @return Theater
     */
    public function addMediaInterview(Media $mediaInterview)
    {
        $this->mediaInterviews[] = $mediaInterview;
    
        return $this;
    }

    /**
     * Remove mediaInterview
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $mediaInterview
     */
    public function removeMediaInterview(Media $mediaInterview)
    {
        $this->mediaInterviews->removeElement($mediaInterview);
    }

    /**
     * Get mediaInterviews
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMediaInterviews()
    {
        return $this->mediaInterviews;
    }
}
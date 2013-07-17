<?php

namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Armd\MainBundle\Model\ChangeHistorySavableInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="armd_real_museum") 
 */
class RealMuseum implements Taggable, ChangeHistorySavableInterface
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;      
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $address;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\AtlasBundle\Entity\Region", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\MuseumBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\AtlasBundle\Entity\Object")
     * @ORM\JoinColumn(name="atlas_object_id", referencedColumnName="id")
     */
    protected $atlasObject;
    
    /**
     * @ORM\ManyToMany(targetEntity="Museum")
     * @ORM\JoinTable(name="armd_real_museum_virtual_tour")
     */
    private $virtualTours;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $email;    
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phone;    
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $schedule;      
    
    private $tags;
    
    /**
     * @ORM\Column(name="corrected", type="boolean", nullable=true)
     */
    protected $corrected;    

    public function __toString()
    {
        return $this->getTitle();
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        $this->tags = $this->tags ? : new ArrayCollection();

        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }
    
    /**
     * @return string
     */
    public function getTaggableType()
    {
        return 'armd_real_museum';
    }
    
    /**
     * @return int
     */
    public function getTaggableId()
    {
        return $this->getId();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->virtualTours = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return RealMuseum
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
     * Set address
     *
     * @param string $address
     * @return RealMuseum
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
     * Set url
     *
     * @param string $url
     * @return RealMuseum
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
     * Set description
     *
     * @param string $description
     * @return RealMuseum
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
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return RealMuseum
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
     * Set region
     *
     * @param \Armd\AtlasBundle\Entity\Region $region
     * @return RealMuseum
     */
    public function setRegion(\Armd\AtlasBundle\Entity\Region $region = null)
    {
        $this->region = $region;
    
        return $this;
    }

    /**
     * Get region
     *
     * @return \Armd\AtlasBundle\Entity\Region 
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set category
     *
     * @param \Armd\MuseumBundle\Entity\Category $category
     * @return RealMuseum
     */
    public function setCategory(\Armd\MuseumBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \Armd\MuseumBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set atlasObject
     *
     * @param \Armd\AtlasBundle\Entity\Object $atlasObject
     * @return RealMuseum
     */
    public function setAtlasObject(\Armd\AtlasBundle\Entity\Object $atlasObject = null)
    {
        $this->atlasObject = $atlasObject;
    
        return $this;
    }

    /**
     * Get atlasObject
     *
     * @return \Armd\AtlasBundle\Entity\Object 
     */
    public function getAtlasObject()
    {
        return $this->atlasObject;
    }

    /**
     * Add virtualTours
     *
     * @param \Armd\MuseumBundle\Entity\Museum $virtualTours
     * @return RealMuseum
     */
    public function addVirtualTour(\Armd\MuseumBundle\Entity\Museum $virtualTours)
    {
        $this->virtualTours[] = $virtualTours;
    
        return $this;
    }

    /**
     * Remove virtualTours
     *
     * @param \Armd\MuseumBundle\Entity\Museum $virtualTours
     */
    public function removeVirtualTour(\Armd\MuseumBundle\Entity\Museum $virtualTours)
    {
        $this->virtualTours->removeElement($virtualTours);
    }

    /**
     * Get virtualTours
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVirtualTours()
    {
        return $this->virtualTours;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return RealMuseum
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
     * @return RealMuseum
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
     * Set schedule
     *
     * @param string $schedule
     * @return RealMuseum
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    
        return $this;
    }

    /**
     * Get schedule
     *
     * @return string 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
    
    /**
     * Set corrected
     *
     * @param boolean $corrected
     * @return RealMuseum
     */
    public function setCorrected($corrected)
    {
        $this->corrected = $corrected;
    
        return $this;
    }

    /**
     * Get corrected
     *
     * @return boolean 
     */
    public function getCorrected()
    {
        return $this->corrected;
    }

    public function getClassName()    
    {
        return get_class($this);
    }    
}
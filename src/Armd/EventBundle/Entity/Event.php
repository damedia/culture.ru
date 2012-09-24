<?php

namespace Armd\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\EventBundle\Entity\Event
 *
 * @ORM\Table(name="armd_event")
 * @ORM\Entity
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
     * @var \DateTime $beginDate
     *
     * @ORM\Column(name="date_from", type="datetime")
     */
    private $beginDate;

    /**
     * @var \DateTime $endDate
     *
     * @ORM\Column(name="date_to", type="datetime", nullable=true)
     */
    private $endDate;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\OrderBy({"title" = "ASC"})     
     **/
    protected $category;    

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\AtlasBundle\Entity\Region")
     */
    private $region;

    /**
     * @var string $city
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;
    
    /**
     * @var string $place
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $place;
    
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */        
    protected $published;        


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
     * Set beginDate
     *
     * @param \DateTime $beginDate
     * @return Event
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;
    
        return $this;
    }

    /**
     * Get beginDate
     *
     * @return \DateTime 
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return Event
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    
        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
    
    /**
     * Set category
     *
     * @param Category $category
     * @return Event
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return Category 
     */
    public function getCategory()
    {
        return $this->category;
    }    

    /**
     * Set city
     *
     * @param string $city
     * @return Event
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set place
     *
     * @param string $place
     * @return Event
     */
    public function setPlace($place)
    {
        $this->place = $place;
    
        return $this;
    }

    /**
     * Get place
     *
     * @return string 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set region
     *
     * @param Armd\AtlasBundle\Entity\Region $region
     * @return Event
     */
    public function setRegion(\Armd\AtlasBundle\Entity\Region $region = null)
    {
        $this->region = $region;
    
        return $this;
    }

    /**
     * Get region
     *
     * @return Armd\AtlasBundle\Entity\Region 
     */
    public function getRegion()
    {
        return $this->region;
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
}
<?php

namespace Armd\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ContentAbstractBundle\Entity\BaseContent;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_monument")
 */
class Monument extends BaseContent implements TaxonomyInterface
{
    /**
     * @ORM\Column(type="string")
     */
    private $title;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $address;        
        
    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="artworks")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     */    
    private $author;
    
    /**
     * @ORM\Column(type="date", name="begin_date", nullable=true)
     */    
    private $beginDate;
        
    /**
     * @ORM\Column(type="date", name="end_date", nullable=true)
     */    
    private $endDate;    
    
    /**
     * @ORM\Column(type="string", name="begin_date_desc", nullable=true)
     */    
    private $beginDateDescription;    
    
    /**
     * @ORM\Column(type="string", name="end_date_desc", nullable=true)
     */    
    private $endDateDescription;            
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $announce;        
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Armd\CultureMapBundle\Entity\Subject")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id", nullable=true)
     */
    private $subject;    
    
    /**
     * @ORM\Column(name="latitude", type="string", length=12, nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(name="longitude", type="string", length=12, nullable=true)
     */
    private $longitude;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Armd\ContentAbstractBundle\Entity\Stream")
     */
    private $stream;        
    
    /**
     * @ORM\Column(type="string", nullable=true, name="_tag")
     */    
    private $personalTag;

    /**
     * @ORM\Column(type="text", nullable=true, name="_tags")
     */    
    private $tags;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Armd\Bundle\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\Bundle\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    private $gallery;    

    /**
     * Set title
     *
     * @param string $title
     * @return Monument
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
     * Set city
     *
     * @param string $city
     * @return Monument
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
     * Set address
     *
     * @param string $address
     * @return Monument
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
     * Set beginDate
     *
     * @param date $beginDate
     * @return Monument
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;
        return $this;
    }

    /**
     * Get beginDate
     *
     * @return date 
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * Set endDate
     *
     * @param date $endDate
     * @return Monument
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Get endDate
     *
     * @return date 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set beginDateDescription
     *
     * @param string $beginDateDescription
     * @return Monument
     */
    public function setBeginDateDescription($beginDateDescription)
    {
        $this->beginDateDescription = $beginDateDescription;
        return $this;
    }

    /**
     * Get beginDateDescription
     *
     * @return string 
     */
    public function getBeginDateDescription()
    {
        return $this->beginDateDescription;
    }

    /**
     * Set endDateDescription
     *
     * @param string $endDateDescription
     * @return Monument
     */
    public function setEndDateDescription($endDateDescription)
    {
        $this->endDateDescription = $endDateDescription;
        return $this;
    }

    /**
     * Get endDateDescription
     *
     * @return string 
     */
    public function getEndDateDescription()
    {
        return $this->endDateDescription;
    }

    /**
     * Set announce
     *
     * @param text $announce
     * @return Monument
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
     * @return Monument
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
     * Set latitude
     *
     * @param string $latitude
     * @return Monument
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return Monument
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * Get longitude
     *
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set personalTag
     *
     * @param string $personalTag
     * @return Monument
     */
    public function setPersonalTag($personalTag)
    {
        $this->personalTag = $personalTag;
        return $this;
    }

    /**
     * Get personalTag
     *
     * @return string 
     */
    public function getPersonalTag()
    {
        return $this->personalTag;
    }

    /**
     * Set tags
     *
     * @param text $tags
     * @return Monument
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Get tags
     *
     * @return text 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set author
     *
     * @param Armd\CommonBundle\Entity\Person $author
     * @return Monument
     */
    public function setAuthor(\Armd\CommonBundle\Entity\Person $author = null)
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Get author
     *
     * @return Armd\CommonBundle\Entity\Person 
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set subject
     *
     * @param Armd\CultureMapBundle\Entity\Subject $subject
     * @return Monument
     */
    public function setSubject(\Armd\CultureMapBundle\Entity\Subject $subject = null)
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * Get subject
     *
     * @return Armd\CultureMapBundle\Entity\Subject 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set stream
     *
     * @param \Armd\Bundle\ContentAbstractBundle\Entity\Stream $stream
     * @return Monument
     */
    public function setStream(\Armd\ContentAbstractBundle\Entity\Stream $stream = null)
    {
        $this->stream = $stream;
        return $this;
    }

    /**
     * Get stream
     *
     * @return Armd\ContentAbstractBundle\Entity\Stream 
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Set image
     *
     * @param Armd\Bundle\MediaBundle\Entity\Media $image
     * @return Monument
     */
    public function setImage(\Armd\Bundle\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return Armd\Bundle\MediaBundle\Entity\Media 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set gallery
     *
     * @param Armd\Bundle\MediaBundle\Entity\Gallery $gallery
     * @return Monument
     */
    public function setGallery(\Armd\Bundle\MediaBundle\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;
        return $this;
    }

    /**
     * Get gallery
     *
     * @return Armd\Bundle\MediaBundle\Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }
}
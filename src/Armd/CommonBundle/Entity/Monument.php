<?php

namespace Armd\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\Bundle\CmsBundle\Entity\BaseContent;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_monument")
 */
class Monument extends BaseContent implements TaxonomyInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */    
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $title;
    
    /**
     * @ORM\Column(type="string", nullable="true")
     */
    private $city;

    /**
     * @ORM\Column(type="string", nullable="true")
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
     * @ORM\Column(type="text", nullable="true")
     */
    private $announce;        
    
    /**
     * @ORM\Column(type="text", nullable="true")
     */
    private $body;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Armd\CultureMapBundle\Entity\Subject")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id", nullable="true")
     */
    private $subject;    
    
    /**
     * @ORM\Column(name="latitude", type="string", length=12, nullable="true")
     */
    private $latitude;

    /**
     * @ORM\Column(name="longitude", type="string", length=12, nullable="true")
     */
    private $longitude;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Armd\Bundle\CmsBundle\Entity\ContentStream")
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
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     */
    public function setCity($city)
    {
        $this->city = $city;
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
     */
    public function setAddress($address)
    {
        $this->address = $address;
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
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;
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
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
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
     * Set announce
     *
     * @param text $announce
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
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
     */
    public function setBody($body)
    {
        $this->body = $body;
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
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
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
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
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
     */
    public function setPersonalTag($personalTag)
    {
        $this->personalTag = $personalTag;
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
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
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
     */
    public function setAuthor(\Armd\CommonBundle\Entity\Person $author)
    {
        $this->author = $author;
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
     */
    public function setSubject(\Armd\CultureMapBundle\Entity\Subject $subject)
    {
        $this->subject = $subject;
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
     * @param Armd\Bundle\CmsBundle\Entity\ContentStream $stream
     */
    public function setStream(\Armd\Bundle\CmsBundle\Entity\ContentStream $stream)
    {
        $this->stream = $stream;
    }

    /**
     * Get stream
     *
     * @return Armd\Bundle\CmsBundle\Entity\ContentStream 
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * Set image
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $image
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image)
    {
        $this->image = $image;
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
     */
    public function setGallery(\Application\Sonata\MediaBundle\Entity\Gallery $gallery)
    {
        $this->gallery = $gallery;
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
     * Set beginDateDescription
     *
     * @param string $beginDateDescription
     */
    public function setBeginDateDescription($beginDateDescription)
    {
        $this->beginDateDescription = $beginDateDescription;
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
     */
    public function setEndDateDescription($endDateDescription)
    {
        $this->endDateDescription = $endDateDescription;
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
}
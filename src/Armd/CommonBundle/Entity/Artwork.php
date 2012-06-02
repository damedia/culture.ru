<?php

namespace Armd\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\Bundle\CmsBundle\Entity\BaseContent;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_artwork")
 */
class Artwork extends BaseContent implements TaxonomyInterface
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
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="artworks")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     */    
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\CommonBundle\Entity\Institution")
     */
    private $place;
    
    /**
     * @ORM\Column(type="date", name="artwork_date", nullable=true)
     */    
    private $date;
    
    /**
     * @ORM\Column(type="string", name="artwork_date_desc", nullable=true)
     */    
    private $dateDescription;    
    
    /**
     * @ORM\Column(type="text", nullable="true")
     */
    private $announce;        
    
    /**
     * @ORM\Column(type="text", nullable="true")
     */
    private $body;
    
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
     * Set date
     *
     * @param date $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return date 
     */
    public function getDate()
    {
        return $this->date;
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
     * Set place
     *
     * @param Armd\CommonBundle\Entity\Institution $place
     */
    public function setPlace(\Armd\CommonBundle\Entity\Institution $place)
    {
        $this->place = $place;
    }

    /**
     * Get place
     *
     * @return Armd\CommonBundle\Entity\Institution 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set dateDescription
     *
     * @param string $dateDescription
     */
    public function setDateDescription($dateDescription)
    {
        $this->dateDescription = $dateDescription;
    }

    /**
     * Get dateDescription
     *
     * @return string 
     */
    public function getDateDescription()
    {
        return $this->dateDescription;
    }
}
<?php

namespace Armd\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ContentAbstractBundle\Entity\BaseContent;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_artwork")
 */
class Artwork extends BaseContent implements TaxonomyInterface
{
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $announce;        
    
    /**
     * @ORM\Column(type="text", nullable=true)
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
     * @return Artwork
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
     * Set date
     *
     * @param date $date
     * @return Artwork
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
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
     * Set announce
     *
     * @param text $announce
     * @return Artwork
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
     * @return Artwork
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
     * Set personalTag
     *
     * @param string $personalTag
     * @return Artwork
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
     * @return Artwork
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
     * @return Artwork
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
     * Set place
     *
     * @param Armd\CommonBundle\Entity\Institution $place
     * @return Artwork
     */
    public function setPlace(\Armd\CommonBundle\Entity\Institution $place = null)
    {
        $this->place = $place;
        return $this;
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
     * Set image
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $image
     * @return Artwork
     */
    public function setImage(\Armd\Bundle\MediaBundle\Entity\Media $image = null)
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
     * @return Artwork
     */
    public function setGallery(\Armd\Bundle\MediaBundle\Entity\Gallery $gallery = null)
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
     * @var integer $id
     */
    private $id;


    /**
     * Set dateDescription
     *
     * @param string $dateDescription
     * @return Artwork
     */
    public function setDateDescription($dateDescription)
    {
        $this->dateDescription = $dateDescription;
        return $this;
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

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
<?php

namespace Armd\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ContentAbstractBundle\Entity\BaseContent;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_person")
 */
class Person extends BaseContent implements TaxonomyInterface
{
    /**
     * @ORM\Column(type="string")
     */
    private $lastname;
    
    /**
     * @ORM\Column(type="string")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $middlename;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $deathday;
    
    /**
     * @ORM\Column(type="string", name="birthday_desc", nullable=true)
     */    
    private $birthdayDescription;            
    
    /**
     * @ORM\Column(type="string", name="deathday_desc", nullable=true)
     */    
    private $deathdayDescription;                
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $birthpalce;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $occupation;
    
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
     * @ORM\OneToMany(targetEntity="Artwork", mappedBy="author")
     */    
    private $artworks;
        
    public function __construct()
    {
        $this->artworks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->getTitle();
    }    
    
    public function getTitle()
    {
        $title = "{$this->getLastname()} {$this->getFirstname()} {$this->getMiddlename()}";
        
        return preg_replace('/\s+/', ' ', $title) ;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Person
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     * @return Person
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set middlename
     *
     * @param string $middlename
     * @return Person
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;
        return $this;
    }

    /**
     * Get middlename
     *
     * @return string 
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * Set birthday
     *
     * @param date $birthday
     * @return Person
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
        return $this;
    }

    /**
     * Get birthday
     *
     * @return date 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set deathday
     *
     * @param date $deathday
     * @return Person
     */
    public function setDeathday($deathday)
    {
        $this->deathday = $deathday;
        return $this;
    }

    /**
     * Get deathday
     *
     * @return date 
     */
    public function getDeathday()
    {
        return $this->deathday;
    }

    /**
     * Set birthpalce
     *
     * @param string $birthpalce
     * @return Person
     */
    public function setBirthpalce($birthpalce)
    {
        $this->birthpalce = $birthpalce;
        return $this;
    }

    /**
     * Get birthpalce
     *
     * @return string 
     */
    public function getBirthpalce()
    {
        return $this->birthpalce;
    }

    /**
     * Set occupation
     *
     * @param text $occupation
     * @return Person
     */
    public function setOccupation($occupation)
    {
        $this->occupation = $occupation;
        return $this;
    }

    /**
     * Get occupation
     *
     * @return text 
     */
    public function getOccupation()
    {
        return $this->occupation;
    }

    /**
     * Set announce
     *
     * @param text $announce
     * @return Person
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
     * @return Person
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
     * @return Person
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
     * @return Person
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
     * Set image
     *
     * @param Armd\Bundle\MediaBundle\Entity\Media $image
     * @return Person
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
     * @return Person
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

    /**
     * Add artworks
     *
     * @param Armd\CommonBundle\Entity\Artwork $artworks
     * @return Person
     */
    public function addArtwork(\Armd\CommonBundle\Entity\Artwork $artworks)
    {
        $this->artworks[] = $artworks;
        return $this;
    }

    /**
     * Get artworks
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getArtworks()
    {
        return $this->artworks;
    }

    /**
     * Set birthdayDescription
     *
     * @param string $birthdayDescription
     */
    public function setBirthdayDescription($birthdayDescription)
    {
        $this->birthdayDescription = $birthdayDescription;
    }

    /**
     * Get birthdayDescription
     *
     * @return string 
     */
    public function getBirthdayDescription()
    {
        return $this->birthdayDescription;
    }

    /**
     * Set deathdayDescription
     *
     * @param string $deathdayDescription
     */
    public function setDeathdayDescription($deathdayDescription)
    {
        $this->deathdayDescription = $deathdayDescription;
    }

    /**
     * Get deathdayDescription
     *
     * @return string 
     */
    public function getDeathdayDescription()
    {
        return $this->deathdayDescription;
    }
}

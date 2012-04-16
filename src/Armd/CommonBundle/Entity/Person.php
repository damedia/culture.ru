<?php

namespace Armd\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\Bundle\CmsBundle\Entity\BaseContent;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_person")
 */
class Person extends BaseContent implements TaxonomyInterface
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $birthpalce;
    
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
     * @ORM\OneToMany(targetEntity="Artwork", mappedBy="author")
     */    
    private $artworks;
        
    public function __construct()
    {
        $this->artworks = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get title
     *
     * @return string 
     */    
    
    public function __toString()
    {
        return $this->getTitle();
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
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        $title = "{$this->getLastname()} {$this->getFirstname()} {$this->getMiddlename()}";
        
        return preg_replace('/\s+/', ' ', $title) ;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
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
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
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
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;
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
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
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
     */
    public function setDeathday($deathday)
    {
        $this->deathday = $deathday;
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
     */
    public function setBirthpalce($birthpalce)
    {
        $this->birthpalce = $birthpalce;
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
     * Add artworks
     *
     * @param Armd\CommonBundle\Entity\Artwork $artworks
     */
    public function addArtwork(\Armd\CommonBundle\Entity\Artwork $artworks)
    {
        $this->artworks[] = $artworks;
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
}
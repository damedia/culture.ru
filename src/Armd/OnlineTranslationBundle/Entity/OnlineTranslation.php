<?php

namespace Armd\OnlineTranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DoctrineExtensions\Taggable\Taggable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Armd\OnlineTranslationBundle\Entity\OnlineTranslation
 *
 * @ORM\Entity
 * @ORM\Table(name="online_translation")
 */
class OnlineTranslation implements Taggable
{
    const TYPE_ANNOUNCE = 0;
    const TYPE_TRANSLATION = 1;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published = false;
    
    /**
     * @ORM\Column(type="smallint", options={"default"=0})
     */
    private $type = 0;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;
    
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $location;
    
    /**
     * @ORM\Column(name="short_description", type="string")
     * @Assert\NotBlank()
     */
    private $shortDescription;
    
    /**
     * @ORM\Column(name="full_description", type="text")
     */
    private $fullDescription;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @Assert\NotBlank()
     */
    private $image;
    
    /**
     * @ORM\Column(name="data_code", type="text")
     * @Assert\NotBlank()
     */
    private $dataCode;

    private $tags;

    public function __toString()
    {
        return $this->getTitle();
    }
    
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
        return 'armd_online_translation';
    }

    /**
     * @return int
     */
    public function getTaggableId()
    {
        return $this->getId();
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
     * Set published
     *
     * @param boolean $published
     * @return OnlineTranslation
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
     * Set title
     *
     * @param string $title
     * @return OnlineTranslation
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
     * @param \DateTime $date
     * @return OnlineTranslation
     */
    public function setDate($date)
    {
        $this->date = $date;
    
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
     * Set location
     *
     * @param string $location
     * @return OnlineTranslation
     */
    public function setLocation($location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set shortDescription
     *
     * @param string $shortDescription
     * @return OnlineTranslation
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    
        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string 
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Set fullDescription
     *
     * @param string $fullDescription
     * @return OnlineTranslation
     */
    public function setFullDescription($fullDescription)
    {
        $this->fullDescription = $fullDescription;
    
        return $this;
    }

    /**
     * Get fullDescription
     *
     * @return string 
     */
    public function getFullDescription()
    {
        return $this->fullDescription;
    }

    /**
     * Set dataCode
     *
     * @param string $dataCode
     * @return OnlineTranslation
     */
    public function setDataCode($dataCode)
    {
        $this->dataCode = $dataCode;
    
        return $this;
    }

    /**
     * Get dataCode
     *
     * @return string 
     */
    public function getDataCode()
    {
        return $this->dataCode;
    }

    /**
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return OnlineTranslation
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
     * Set type
     *
     * @param integer $type
     * @return OnlineTranslation
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
}
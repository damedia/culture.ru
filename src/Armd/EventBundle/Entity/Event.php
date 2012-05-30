<?php

namespace Armd\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\NewsBundle\Entity\BaseNews;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;

/**
 * @ORM\Entity(repositoryClass="Armd\EventBundle\Repository\EventRepository")
 * @ORM\Table(name="content_event")
 */
class Event extends BaseNews
//implements TaxonomyInterface
{
    /**
     * @ORM\ManyToOne(targetEntity="Armd\CommonBundle\Entity\Institution")
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\Bundle\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\Bundle\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="collage_image_id", referencedColumnName="id")
     */
    private $collageImage;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\Bundle\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    private $gallery;

    /** 
     * @ORM\OneToMany(targetEntity="Schedule", mappedBy="event") 
     */
    private $schedule;
    
    /**
     * @ORM\Column(type="string", nullable=true, name="_tag")
     */    
    private $personalTag;

    /**
     * @ORM\Column(type="text", nullable=true, name="_tags")
     */    
    private $tags;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="show_in_collage")
     */    
    private $showInCollage;
    
    public function __toString()
    {
        return $this->getTitle();
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
     * Set image
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $image
     */
    public function setImage(\Armd\Bundle\MediaBundle\Entity\Media $image)
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
    public function setGallery(\Armd\Bundle\MediaBundle\Entity\Gallery $gallery)
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

    public function __construct()
    {
        $this->schedule = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add schedule
     *
     * @param Armd\EventBundle\Entity\Schedule $schedule
     */
    public function addSchedule(\Armd\EventBundle\Entity\Schedule $schedule)
    {
        $this->schedule[] = $schedule;
    }

    /**
     * Get schedule
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set collageImage
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $collageImage
     */
    public function setCollageImage(\Armd\Bundle\MediaBundle\Entity\Media $collageImage)
    {
        $this->collageImage = $collageImage;
    }

    /**
     * Get collageImage
     *
     * @return Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getCollageImage()
    {
        return $this->collageImage;
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
     * Set showInCollage
     *
     * @param boolean $showInCollage
     */
    public function setShowInCollage($showInCollage)
    {
        $this->showInCollage = $showInCollage;
    }

    /**
     * Get showInCollage
     *
     * @return boolean 
     */
    public function getShowInCollage()
    {
        return $this->showInCollage;
    }
}
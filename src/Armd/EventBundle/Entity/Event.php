<?php

namespace Armd\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\Bundle\NewsBundle\Entity\NewsMappedSuperclass as BaseNews;

/**
 * @ORM\Entity(repositoryClass="Armd\EventBundle\Repository\EventRepository")
 * @ORM\Table(name="content_event")
 */
class Event extends BaseNews
{
    /**
     * @ORM\ManyToOne(targetEntity="Armd\CommonBundle\Entity\Institution")
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="collage_image_id", referencedColumnName="id")
     */
    private $collageImage;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
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
    public function setCollageImage(\Application\Sonata\MediaBundle\Entity\Media $collageImage)
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
}
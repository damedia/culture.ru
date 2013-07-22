<?php

namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Armd\MainBundle\Model\ChangeHistorySavableInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="armd_war_gallery_member") 
 * @ORM\HasLifecycleCallbacks()
 */
class WarGalleryMember implements ChangeHistorySavableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published = true;

    /**
     * @ORM\Column(type="text")
     */
    protected $name;
    
    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $years;
    
    /**
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $ranks;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;       
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     */
    private $preview;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     */
    private $image;
    
    /**
     * @ORM\Column(name="corrected", type="boolean", nullable=true)
     */
    protected $corrected;    

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
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return \Armd\MuseumBundle\Entity\WarGalleryMember
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return \Armd\MuseumBundle\Entity\WarGalleryMember
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get years
     *
     * @return string 
     */
    public function getYears()
    {
        return $this->years;
    }

    /**
     * Set years
     *
     * @param string $years
     * @return \Armd\MuseumBundle\Entity\WarGalleryMember
     */
    public function setYears($years)
    {
        $this->years = $years;
    
        return $this;
    }

    /**
     * Get ranks
     *
     * @return string 
     */
    public function getRanks()
    {
        return $this->ranks;
    }

    /**
     * Set ranks
     *
     * @param string $ranks
     * @return \Armd\MuseumBundle\Entity\WarGalleryMember
     */
    public function setRanks($ranks)
    {
        $this->ranks = $ranks;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return \Armd\MuseumBundle\Entity\WarGalleryMember 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return \Armd\MuseumBundle\Entity\WarGalleryMember
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get preview
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * Set preview
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $preview
     * @return \Armd\MuseumBundle\Entity\WarGalleryMember
     */
    public function setPreview(Media $preview = null)
    {
        $this->preview = $preview;
    
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
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return \Armd\MuseumBundle\Entity\WarGalleryMember
     */
    public function setImage(Media $image = null)
    {
        $this->image = $image;
    
        return $this;
    }
    
    /**
     * Set corrected
     *
     * @param boolean $corrected
     * @return WarGalleryMember
     */
    public function setCorrected($corrected)
    {
        $this->corrected = $corrected;
    
        return $this;
    }

    /**
     * Get corrected
     *
     * @return boolean 
     */
    public function getCorrected()
    {
        return $this->corrected;
    }

    public function getClassName()    
    {
        return get_class($this);
    }    
}
<?php

namespace Armd\ExhibitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Armd\ExhibitBundle\Entity\ArtObject
 *
 * @ORM\Entity()
 * @ORM\Table(name="art_object")
 */
class ArtObject implements Taggable
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var date $event_date
     *
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @var integer $century
     *
     * @ORM\Column(type="text")
     */
    private $description;
    
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */        
    protected $published;
        
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="primary_image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"})
     * @ORM\JoinTable(name="art_object_video")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $videos;
    
    /**
     * @ORM\ManyToOne(targetEntity="Armd\MuseumBundle\Entity\RealMuseum")
     * @ORM\JoinColumn(name="museum_id", referencedColumnName="id")
     */
    private $museum;
    
    /**
     * @ORM\ManyToMany(targetEntity="Category", cascade={"persist"})
     * @ORM\JoinTable(name="art_object_category")
     */
    private $categories;
    
    private $tags;

    public function __toString()
    {
        return $this->getTitle();
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->videos = new ArrayCollection();
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
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
        return 'armd_art_object';
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
     * Set title
     *
     * @param string $title
     * @return ArtObject
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
     * @return ArtObject
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
     * Set description
     *
     * @param string $description
     * @return ArtObject
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return ArtObject
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
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return ArtObject
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
     * Add videos
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $videos
     * @return ArtObject
     */
    public function addVideo(\Armd\TvigleVideoBundle\Entity\TvigleVideo $videos)
    {
        $this->videos[] = $videos;
    
        return $this;
    }

    /**
     * Remove videos
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $videos
     */
    public function removeVideo(\Armd\TvigleVideoBundle\Entity\TvigleVideo $videos)
    {
        $this->videos->removeElement($videos);
    }

    /**
     * Get videos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * Set museum
     *
     * @param \Armd\MuseumBundle\Entity\RealMuseum $museum
     * @return ArtObject
     */
    public function setMuseum(\Armd\MuseumBundle\Entity\RealMuseum $museum = null)
    {
        $this->museum = $museum;
    
        return $this;
    }

    /**
     * Get museum
     *
     * @return \Armd\MuseumBundle\Entity\RealMuseum 
     */
    public function getMuseum()
    {
        return $this->museum;
    }

    /**
     * Add category
     *
     * @param \Armd\ExhibitBundle\Entity\Category $categories
     * @return ArtObject
     */
    public function addCategory(\Armd\ExhibitBundle\Entity\Category $category)
    {
        $this->categories[] = $category;
    
        return $this;
    }

    /**
     * Remove category
     *
     * @param \Armd\ExhibitBundle\Entity\Category $categories
     */
    public function removeCategory(\Armd\ExhibitBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
<?php

namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Sonata\MediaBundle\Entity\Media;

/**
 * @ORM\Entity()
 * @ORM\Table(name="armd_museum") 
 */
class Museum
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    /**
     * @ORM\Column(type="string")
     */    
    protected $url;
    
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */        
    protected $published;    
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="banner_image_id", referencedColumnName="id")
     */
    private $bannerImage;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\AtlasBundle\Entity\Region", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\MuseumBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\AtlasBundle\Entity\Object")
     * @ORM\JoinColumn(name="atlas_object_id", referencedColumnName="id")
     */
    protected $atlasObject;

    /**
     * @ORM\Column(name="show_on_main", type="boolean", nullable=false)
     */
    private $showOnMain = false;
    
    /**
     * @ORM\Column(name="show_on_main_ord", type="integer", nullable=false)
     */
    private $showOnMainOrd = 0;
    
    /**
     * @ORM\Column(name="sort", type="integer", nullable=false, options={"default"=0})
     */
    private $sort = 0;

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
     * Set title
     *
     * @param string $title
     * @return Museum
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
     * Set url
     *
     * @param string $url
     * @return Museum
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return Museum
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Museum
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
     * @param Application\Sonata\MediaBundle\Entity\Media $image
     * @return Museum
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
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
     * @return mixed
     */
    public function getBannerImage()
    {
        return $this->bannerImage;
    }

    /**
     * @param $bannerImage
     */
    public function setBannerImage(Media $bannerImage = null)
    {
        $this->bannerImage = $bannerImage;
    }

    /**
     * Set region
     *
     * @param \Armd\AtlasBundle\Entity\Region $region
     * @return Museum
     */
    public function setRegion(\Armd\AtlasBundle\Entity\Region $region = null)
    {
        $this->region = $region;
    
        return $this;
    }

    /**
     * Get region
     *
     * @return \Armd\AtlasBundle\Entity\Region 
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set atlasObject
     *
     * @param \Armd\AtlasBundle\Entity\Object $atlasObject
     * @return Museum
     */
    public function setAtlasObject(\Armd\AtlasBundle\Entity\Object $atlasObject = null)
    {
        $this->atlasObject = $atlasObject;
    
        return $this;
    }

    /**
     * Get atlasObject
     *
     * @return \Armd\AtlasBundle\Entity\Object 
     */
    public function getAtlasObject()
    {
        return $this->atlasObject;
    }

    /**
     * Set category
     *
     * @param \Armd\MuseumBundle\Entity\Category $category
     * @return Museum
     */
    public function setCategory(\Armd\MuseumBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \Armd\MuseumBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return boolean
     */
    public function getShowOnMain()
    {
        $this->showOnMain = $this->showOnMain;

        return $this->showOnMain;
    }

    public function setShowOnMain($showOnMain)
    {
        $this->showOnMain = $showOnMain;

        return $this;
    }

    /**
     * @return integer
     */
    public function getShowOnMainOrd()
    {
        $this->showOnMainOrd = $this->showOnMainOrd;

        return $this->showOnMainOrd;
    }

    public function setShowOnMainOrd($showOnMainOrd)
    {
        $this->showOnMainOrd = $showOnMainOrd;

        return $this;
    }
    
    /**
     * @return integer
     */
    public function getSort()
    {
        return $this->sort;
    }

    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }  
}
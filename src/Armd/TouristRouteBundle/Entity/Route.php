<?php

namespace Armd\TouristRouteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

use Application\Sonata\MediaBundle\Entity\Media;

/**
 * Armd\TouristRouteBundle\Entity\Route
 *
 * @ORM\Table(name="tourist_route")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Route implements Taggable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="published", type="boolean")
     */
    private $published = true;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToMany(targetEntity="Category", cascade={"persist"})
     * @ORM\JoinTable(name="tourist_route__tourist_route_category")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="tourist_route__media_image")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $images;

    /**
     * @ORM\ManyToMany(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"})
     * @ORM\JoinTable(name="tourist_route__tvigle_video")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $videos;

    /**
     * @ORM\ManyToMany(targetEntity="\Armd\AtlasBundle\Entity\Region")
     * @ORM\JoinTable(name="tourist_route__atlas_region")
     */
    private $regions;

    /**
     * @ORM\ManyToMany(targetEntity="\Armd\AtlasBundle\Entity\Object")
     * @ORM\JoinTable(name="tourist_route__atlas_object")
     */
    private $objects;

    /**
     * @ORM\ManyToMany(targetEntity="Point", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="tourist_route__tourist_route_point")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $points;

    /**
     * @ORM\Column(name="type", type="string", length=10, nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="Route")
     * @ORM\JoinTable(name="tourist_route__tourist_route")
     */
    private $routes;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="banner_id", referencedColumnName="id")
     */
    private $banner;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var Doctrine\Common\Collections\Collection
     */
    private $tags;
    
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->regions = new ArrayCollection();
        $this->objects = new ArrayCollection();
        $this->points = new ArrayCollection();
        $this->routes = new ArrayCollection();
        $this->createdAt = $this->updatedAt = new \DateTime('now');
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Get icon
     * 
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getIcon()
    {
        $category = $this->getCategory();
        $icon     = null;

        if ($category) {
            $icon = $category->getIcon();
        }

        return $icon;
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
     * Get published
     *
     * @return bool
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set publiched
     *
     * @param bool $published
     * @return Route
     */
    public function setPublished($published)
    {
        $this->published = $published;

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
     * Set title
     *
     * @param string $title
     * @return Route
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Route
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
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

    /**
     * Set categories
     *
     * @param \Doctrine\Common\Collections\Collection $categories
     * @return Route
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Add category
     *
     * @param Category $category
     * @return Route
     */
    public function addCategory($category)
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    /**
     * Remove category
     *
     * @param Category $category
     */
    public function removeCategory($category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set images
     *
     * @param \Doctrine\Common\Collections\Collection $images
     * @return Route
     */
    public function setImages($images)
    {
        $this->images = $images;

        return $this;
    }

    /**
     * Add image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return Route
     */
    public function addImage($image)
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    /**
     * Remove image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     */
    public function removeImage($image)
    {
        $this->images->removeElement($image);
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
     * Set videos
     *
     * @param \Doctrine\Common\Collections\Collection $videos
     * @return Route
     */
    public function setVideos($videos)
    {
        $this->videos = $videos;

        return $this;
    }

    /**
     * Add video
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $video
     * @return Route
     */
    public function addVideo(\Armd\TvigleVideoBundle\Entity\TvigleVideo $video)
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
        }

        return $this;
    }

    /**
     * Remove video
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $video
     */
    public function removeVideo(\Armd\TvigleVideoBundle\Entity\TvigleVideo $video)
    {
        $this->videos->removeElement($video);
    }

    /**
     * Get regions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * Set regions
     *
     * @param \Doctrine\Common\Collections\Collection $regions
     * @return Route
     */
    public function setRegions($regions)
    {
        $this->regions = $regions;

        return $this;
    }

    /**
     * Add region
     *
     * @param \Armd\AtlasBundle\Entity\Region $region
     * @return Route
     */
    public function addRegion(\Armd\AtlasBundle\Entity\Region $region)
    {
        if (!$this->regions->contains($region)) {
            $this->regions[] = $region;
        }

        return $this;
    }

    /**
     * Remove region
     *
     * @param \Armd\AtlasBundle\Entity\Region $region
     */
    public function removeRegion(\Armd\AtlasBundle\Entity\Region $region)
    {
        $this->regions->removeElement($region);
    }

    /**
     * Get objects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Set objects
     *
     * @param \Doctrine\Common\Collections\Collection $objects
     * @return Route
     */
    public function setObjects($objects)
    {
        $this->objects = $objects;

        return $this;
    }

    /**
     * Add object
     *
     * @param \Armd\AtlasBundle\Entity\Object $object
     * @return Route
     */
    public function addObject(\Armd\AtlasBundle\Entity\Object $object)
    {
        if (!$this->objects->contains($object)) {
            $this->objects[] = $object;
        }

        return $this;
    }

    /**
     * Remove object
     *
     * @param \Armd\AtlasBundle\Entity\Object $object
     */
    public function removeObject(\Armd\AtlasBundle\Entity\Object $object)
    {
        $this->objects->removeElement($object);
    }

    /**
     * Get points
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set points
     *
     * @param \Doctrine\Common\Collections\Collection $points
     * @return Route
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Add point
     *
     * @param Point $point
     * @return Route
     */
    public function addPoint(Point $point)
    {
        if (!$this->points->contains($point)) {
            $this->points[] = $point;
        }

        return $this;
    }

    /**
     * Remove point
     *
     * @param Point $point
     */
    public function removePoint(Point $point)
    {
        $this->points->removeElement($point);
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Route
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get routes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Set routes
     *
     * @param \Doctrine\Common\Collections\Collection $routes
     * @return Route
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * Add route
     *
     * @param Route $route
     * @return Route
     */
    public function addRoute(Route $route)
    {
        if (!$this->routes->contains($route)) {
            $this->routes[] = $route;
        }

        return $this;
    }

    /**
     * Remove route
     *
     * @param Route $route
     */
    public function removeRoute(Route $route)
    {
        $this->regions->removeElement($route);
    }

    /**
     * Get banner
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set banner
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $banner
     * @return Route
     */
    public function setBanner(\Application\Sonata\MediaBundle\Entity\Media $banner = null)
    {
        if (is_null($banner) || $banner->isUploaded()) {
            $this->banner = $banner;
        }

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Route
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return Armd\UserBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdBy
     *
     * @param Armd\UserBundle\Entity\User $createdBy
     * @return Route
     */
    public function setCreatedBy(\Armd\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Route
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return Armd\UserBundle\Entity\User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set updatedBy
     *
     * @param Armd\UserBundle\Entity\User $updatedBy
     * @return Route
     */
    public function setUpdatedBy(\Armd\UserBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Hook on pre-persist operations
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * Hook on pre-update operations
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        $this->tags = $this->tags ? : new ArrayCollection();

        return $this->tags;
    }

    /**
     * Set tags
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection $tags
     * @return Route
     */
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
        return 'armd_tourist_route';
    }

    /**
     * @return int
     */
    public function getTaggableId()
    {
        return $this->getId();
    }
}

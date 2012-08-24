<?php

namespace Armd\AtlasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Armd\AtlasBundle\Entity\Object
 *
 * @ORM\Table(name="atlas_object")
 * @ORM\Entity(repositoryClass="Armd\AtlasBundle\Repository\ObjectRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Object
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="announce", type="text", nullable=true)
     */
    private $announce;

    /**
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(name="site_url", type="string", length=255, nullable=true)
     */
    private $siteUrl;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(name="lat", type="decimal", precision=15, scale=10, nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(name="lon", type="decimal", precision=15, scale=10, nullable=true)
     */
    private $lon;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="objects", cascade={"persist"})
     * @ORM\JoinColumn(name="primary_atlas_category_id", nullable=true)
     */
    private $primaryCategory;

    /**
     * @ORM\ManyToMany(targetEntity="Category", cascade={"persist"})
     * @ORM\JoinTable(name="atlas_category_object")
     */
    private $secondaryCategories;

    /**
     * @ORM\Column(name="work_time", type="string", length=255, nullable=true)
     */
    private $workTime;

    /**
     * @ORM\ManyToMany(targetEntity="WeekDay")
     * @ORM\JoinTable(name="atlas_object_weekend")
     */
    private $weekends;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="primary_image_id", referencedColumnName="id")
     */
    private $primaryImage;


    /**
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="atlas_object_image")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $images;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="atlas_object_archive_image")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $archiveImages;

    /**
     * @ORM\ManyToMany(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"})
     * @ORM\JoinTable(name="atlas_object_video")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $videos;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="image3d_id", referencedColumnName="id")
     */
    private $image3d;

    /**
     * @ORM\Column(name="virtual_tour", type="string", length=255, nullable=true)
     */
    private $virtualTour;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="virtual_tour_image_id", referencedColumnName="id")
     */
    private $virtualTourImage;

    /**
     * @ORM\Column(name="show_at_homepage", type="boolean", nullable=true)
     */
    private $showAtHomepage = false;

    /**
     * @ORM\Column(name="show_at_russian_image", type="boolean", nullable=true)
     */
    private $showAtRussianImage = false;

    /**
     * @ORM\OneToMany(targetEntity="Literature", mappedBy="object", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $literatures;

    /**
     * @ORM\OneToMany(targetEntity="ObjectHint", mappedBy="object", cascade={"all"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $objectHints;

    /**
     * @ORM\ManyToMany(targetEntity="Region")
     * @ORM\JoinTable(name="atlas_object_region")
     */
    private $regions;


    public function __construct()
    {
        $this->secondaryCategories = new ArrayCollection();
        $this->weekends = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->archiveImages = new ArrayCollection();
        $this->literatures = new ArrayCollection();
        $this->objectHints = new ArrayCollection();
        $this->regions = new ArrayCollection();
    }

    public function getIcon()
    {
        $category = $this->getPrimaryCategory();
        $icon = null;
        if ($category) {
            $icon = $category->getIconMedia();
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
     * Set title
     *
     * @param string $title
     * @return Object
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
     * Set lat
     *
     * @param float $lat
     * @return Object
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lon
     *
     * @param float $lon
     * @return Object
     */
    public function setLon($lon)
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * Get lon
     *
     * @return float
     */
    public function getLon()
    {
        return $this->lon;
    }

    /**
     * Add category
     *
     * @param \Armd\AtlasBundle\Entity\Category $category
     * @return Object
     */
    public function addSecondaryCategory(\Armd\AtlasBundle\Entity\Category $category)
    {
        $this->secondaryCategories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Armd\AtlasBundle\Entity\Category $category
     */
    public function removeSecondaryCategory(\Armd\AtlasBundle\Entity\Category $category)
    {
        $this->secondaryCategories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSecondaryCategories()
    {
        return $this->secondaryCategories;
    }

    /**
     * @return mixed
     */
    public function getPrimaryCategory()
    {
        return $this->primaryCategory;
    }

    /**
     * @param Category $primaryCategory
     * @return \Armd\AtlasBundle\Entity\Object
     */
    public function setPrimaryCategory(\Armd\AtlasBundle\Entity\Category $primaryCategory = null)
    {
        $this->primaryCategory = $primaryCategory;
        return $this;
    }


    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        $categories = new ArrayCollection();
        $categories[] = $this->getPrimaryCategory();
        foreach ($this->getSecondaryCategories() as $secondaryCategory) {
            $categories[] = $secondaryCategory;
        }

        return $categories;
    }

    /**
     * Set announce
     *
     * @param string $announce
     * @return Object
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;

        return $this;
    }

    /**
     * Get announce
     *
     * @return string
     */
    public function getAnnounce()
    {
        return $this->announce;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Object
     */
    public function setContent($content)
    {
        $this->content = $content;

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
     * Set siteUrl
     *
     * @param string $siteUrl
     * @return Object
     */
    public function setSiteUrl($siteUrl)
    {
        $this->siteUrl = $siteUrl;

        return $this;
    }

    /**
     * Get siteUrl
     *
     * @return string
     */
    public function getSiteUrl()
    {
        return $this->siteUrl;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Object
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return Object
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Object
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set workTime
     *
     * @param string $workTime
     * @return Object
     */
    public function setWorkTime($workTime)
    {
        $this->workTime = $workTime;
        return $this;
    }

    /**
     * Get workTime
     *
     * @return string
     */
    public function getWorkTime()
    {
        return $this->workTime;
    }

    /**
     * Set virtualTour
     *
     * @param string $virtualTour
     * @return Object
     */
    public function setVirtualTour($virtualTour)
    {
        $this->virtualTour = $virtualTour;
        return $this;
    }

    /**
     * Get virtualTour
     *
     * @return string
     */
    public function getVirtualTour()
    {
        return $this->virtualTour;
    }

    /**
     * Set showAtHomepage
     *
     * @param boolean $showAtHomepage
     * @return Object
     */
    public function setShowAtHomepage($showAtHomepage)
    {
        $this->showAtHomepage = $showAtHomepage;
        return $this;
    }

    /**
     * Set virtualTourImage
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $virtualTourImage
     * @return Object
     */
    public function setVirtualTourImage(\Application\Sonata\MediaBundle\Entity\Media $virtualTourImage = null)
    {
        // SonataAdmin adds empty Media if image3d embedded form is not filled, so check it
        if (is_null($virtualTourImage) || $virtualTourImage->isUploaded()) {
            $this->virtualTourImage = $virtualTourImage;
        }
        return $this;
    }

    /**
     * Get image3d
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getVirtualTourImage()
    {
        return $this->virtualTourImage;
    }

    /**
     * Get showAtHomepage
     *
     * @return boolean
     */
    public function getShowAtHomepage()
    {
        return $this->showAtHomepage;
    }

    /**
     * Add weekends
     *
     * @param \Armd\AtlasBundle\Entity\WeekDay $weekends
     * @return Object
     */
    public function addWeekend(\Armd\AtlasBundle\Entity\WeekDay $weekend)
    {
        $this->weekends[] = $weekend;
        return $this;
    }

    /**
     * Remove weekends
     *
     * @param <variableType$weekends
     */
    public function removeWeekend(\Armd\AtlasBundle\Entity\WeekDay $weekends)
    {
        $this->weekends->removeElement($weekends);
    }

    /**
     * Get weekends
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWeekends()
    {
        return $this->weekends;
    }

    /**
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getPrimaryImage()
    {
        return $this->primaryImage;

    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Media $primaryImage
     * @return Object
     */
    public function setPrimaryImage(\Application\Sonata\MediaBundle\Entity\Media $primaryImage = null)
    {
        if (is_null($primaryImage) || $primaryImage->isUploaded()) {
            $this->primaryImage = $primaryImage;
        }
        return $this;
    }

    /**
     * Add videos
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $videos
     * @return Object
     */
    public function addVideo(\Armd\TvigleVideoBundle\Entity\TvigleVideo $video)
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
        }
        return $this;
    }

    /**
     * Remove videos
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $videos
     */
    public function removeVideo(\Armd\TvigleVideoBundle\Entity\TvigleVideo $video)
    {
        $this->videos->removeElement($video);
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

    public function setVideos($videos)
    {
        $this->videos = $videos;
    }


    /**
     * Set image3d
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image3d
     * @return Object
     */
    public function setImage3d(\Application\Sonata\MediaBundle\Entity\Media $image3d = null)
    {
        // SonataAdmin adds empty Media if image3d embedded form is not filled, so check it
        if (is_null($image3d) || $image3d->isUploaded()) {
            $this->image3d = $image3d;
        }
        return $this;
    }

    /**
     * Get image3d
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getImage3d()
    {
        return $this->image3d;
    }


    public function getImages()
    {
        return $this->images;
    }

    public function setImages($images)
    {
        $this->images = $images;
    }

    public function removeImage($image)
    {
        $this->images->removeElement($image);
    }

    public function addImage($image)
    {
        $this->images[] = $image;
    }

    public function getArchiveImages()
    {
        return $this->archiveImages;
    }

    public function setArchiveImages($archiveImages)
    {
        $this->archiveImages = $archiveImages;
    }

    public function removeArchiveImage($archiveImage)
    {
        $this->archiveImages->removeElement($archiveImage);
    }

    public function addArchiveImage($archiveImage)
    {
        $this->archiveImages[] = $archiveImage;
    }

    public function getShowAtRussianImage()
    {
        return $this->showAtRussianImage;
    }

    public function setShowAtRussianImage($showAtRussianImage)
    {
        $this->showAtRussianImage = $showAtRussianImage;
    }

    public function getLiteratures()
    {
        return $this->literatures;
    }

    public function setLiteratures($literatures)
    {
        foreach ($literatures as $literature) {
            $literature->setObject($this);
        }
        $this->literatures = $literatures;
    }

    public function addLiteratures($literatures)
    {
        if (is_array($literatures) || ($literatures instanceof ArrayCollection)) {
            foreach ($literatures as $literature) {
                $this->addLiterature($literature);
            }

        }
        else {
            $this->addLiterature($literatures);
        }
    }

    public function addLiterature(Literature $literature)
    {
        $literature->setObject($this);
        $this->literatures->add($literature);
    }

    public function removeLiterature(Literature $literature)
    {
        $literature->setObject(null);
        $this->literatures->removeElement($literature);
    }

    public function getObjectHints()
    {
        return $this->objectHints;
    }

    public function setObjectHints($objectHints)
    {
        foreach ($objectHints as $objectHint) {
            $objectHint->setObject($this);
        }
        $this->objectHints = $objectHints;
    }

    public function addObjectHints($objectHints)
    {
        if (is_array($objectHints) || ($objectHints instanceof ArrayCollection)) {
            foreach ($objectHints as $objectHint) {
                $this->addObjectHint($objectHint);
            }

        }
        else {
            $this->addObjectHint($objectHints);
        }
    }

    public function addObjectHint(ObjectHint $objectHint)
    {
        $objectHint->setObject($this);
        $this->objectHints[] = $objectHint;
    }

    public function removeObjectHint(ObjectHint $objectHint)
    {
        $objectHint->setObject(null);
        $this->objectHints->removeElement($objectHint);
    }

    public function getRegions()
    {
        return $this->regions;
    }

    public function setRegions($regions)
    {
        $this->regions = $regions;
    }

    /**
     * Add region
     *
     * @param Region $region
     * @return Object
     */
    public function addRegion(Region $region)
    {
        $this->regions[] = $region;

        return $this;
    }

    /**
     * Remove region
     *
     * @param Region $region
     */
    public function removeRegion(Region $region)
    {
        $this->regions->removeElement($region);
    }

}
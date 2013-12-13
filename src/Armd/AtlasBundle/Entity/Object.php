<?php

namespace Armd\AtlasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Application\Sonata\MediaBundle\Entity\Media;
use Armd\MainBundle\Model\ChangeHistorySavableInterface;
/**
 * Armd\AtlasBundle\Entity\Object
 *
 * @ORM\Table(name="atlas_object")
 * @ORM\Entity(repositoryClass="Armd\AtlasBundle\Repository\ObjectRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Object implements Taggable, ChangeHistorySavableInterface
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="dcx_id", type="string", nullable=true)
     */
    private $dcxId;
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
     * @ORM\Column(name="announce", type="text", nullable=true)
     * @Assert\NotBlank()
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
     * @ORM\ManyToOne(targetEntity="Category", cascade={"persist"})
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
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="primary_image_id", referencedColumnName="id")
     */
    private $primaryImage;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="side_banner_image_id", referencedColumnName="id")
     */
    private $sideBannerImage;

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
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinTable(name="atlas_object_media_video")
     * @ORM\OrderBy({"id" = "ASC"})
     */
    private $mediaVideos;

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
     * @ORM\ManyToMany(targetEntity="Armd\MuseumBundle\Entity\Museum")
     * @ORM\JoinTable(name="atlas_object_virtual_tour")
     */
    private $virtualTours;

    /**
     * @ORM\Column(name="show_at_russian_image", type="boolean", nullable=true)
     */
    private $showAtRussianImage = false;

    /**
     * @ORM\Column(name="russia_image_announce", type="text", nullable=true)
     */
    private $russiaImageAnnounce;

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
     * @ORM\Column(name="is_official", type="boolean", nullable=true)
     */
    private $isOfficial = true;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\AtlasBundle\Entity\ObjectStatus")
     * @ORM\JoinColumn(name="status", referencedColumnName="id")
     */
    private $status;

    /**
     * @ORM\Column(name="reason", type="text", nullable=true)
     */
    private $reason;

    /**
     * @ORM\Column(name="seo_title", type="string", nullable=true)
     */
    private $seoTitle;

    /**
     * @ORM\Column(name="seo_description", type="text", nullable=true)
     */
    private $seoDescription;

    /**
     * @ORM\Column(name="seo_keywords", type="text", nullable=true)
     */
    private $seoKeywords;

    private $tags;

    /**
     * @ORM\Column(name="show_on_main", type="boolean", nullable=false)
     */
    private $showOnMain = false;
    
    /**
     * @ORM\Column(name="show_on_main_ord", type="integer", nullable=false)
     */
    private $showOnMainOrd = 0;
    

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinTable(name="atlas_object_stuff")
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stuff;

    /**
     * @ORM\ManyToMany(targetEntity="\Armd\AtlasBundle\Entity\TouristCluster")
     * @ORM\JoinTable(name="atlas_object_tourist_cluster")
     */
    private $touristCluster;
    
    /**
     * @ORM\Column(name="corrected", type="boolean", nullable=true)
     */
    protected $corrected;
    
    
    public function __toString()
    {
        return $this->getTitle();
    }

    public function syncPrimaryAndSecondaryCategories()
    {
        if (!empty($this->primaryCategory)
            && !$this->secondaryCategories->contains($this->primaryCategory)
        ) {
            $this->secondaryCategories->add($this->primaryCategory);
        }
    }

    public function __construct()
    {
        $this->secondaryCategories = new ArrayCollection();
        $this->weekends = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->mediaVideos = new ArrayCollection();
        $this->archiveImages = new ArrayCollection();
        $this->literatures = new ArrayCollection();
        $this->objectHints = new ArrayCollection();
        $this->regions = new ArrayCollection();
        $this->createdAt = $this->updatedAt = new \DateTime("now");
        $this->virtualTours = new ArrayCollection();
        $this->stuff = new ArrayCollection();
        $this->touristCluster = new ArrayCollection();
    }

    public function setStuff($stuff)
    {
        $this->stuff = $stuff;
    }

    public function addStuff(Media $stuff)
    {
        $this->stuff->add($stuff);
    }

    public function removeStuff(Media $stuff)
    {
        $this->stuff->removeElement($stuff);
    }

    public function getStuff()
    {
        return $this->stuff;
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

    public function getDcxId()
    {
        return $this->dcxId;
    }

    public function setDcxId($dcx_id)
    {
        $this->dcxId = $dcx_id;
        
        return $this;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
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

    public function setVirtualTours($virtualTours)
    {
        $this->virtualTours = $virtualTours;
    }

    public function addVirtualTour(\Armd\MuseumBundle\Entity\Museum $museum)
    {
        $this->virtualTours[] = $museum;

        return $this;
    }

    public function getVirtualTours()
    {
        return $this->virtualTours;
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
     * @return mixed
     */
    public function getSideBannerImage()
    {
        return $this->sideBannerImage;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Media $bannerImage
     * @return Object
     */
    public function setSideBannerImage(\Application\Sonata\MediaBundle\Entity\Media $bannerImage = null)
    {
        if (is_null($bannerImage) || $bannerImage->isUploaded()) {
            $this->sideBannerImage = $bannerImage;
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


    public function getMediaVideos()
    {
        return $this->mediaVideos;
    }

    public function setMediaVideos($mediaVideos)
    {
        $this->mediaVideos = $mediaVideos;
    }

    public function removeMediaVideo($mediaVideo)
    {
        $this->mediaVideos->removeElement($mediaVideo);
    }

    public function addMediaVideo($mediaVideo)
    {
        $this->mediaVideos[] = $mediaVideo;
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

    public function getRussiaImageAnnounce()
    {
        return $this->russiaImageAnnounce;
    }

    public function setRussiaImageAnnounce($russiaImageAnnounce)
    {
        $this->russiaImageAnnounce = $russiaImageAnnounce;
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

        } else {
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
        } else {
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


    /**
     * Add secondaryCategories
     *
     * @param Armd\AtlasBundle\Entity\Category $secondaryCategories
     * @return Object
     */
    public function addSecondaryCategorie(\Armd\AtlasBundle\Entity\Category $secondaryCategories)
    {
        $this->secondaryCategories[] = $secondaryCategories;

        return $this;
    }

    /**
     * Remove secondaryCategories
     *
     * @param Armd\AtlasBundle\Entity\Category $secondaryCategories
     */
    public function removeSecondaryCategorie(\Armd\AtlasBundle\Entity\Category $secondaryCategories)
    {
        $this->secondaryCategories->removeElement($secondaryCategories);
    }

    /**
     * Set createdBy
     *
     * @param Armd\UserBundle\Entity\User $createdBy
     * @return Object
     */
    public function setCreatedBy(\Armd\UserBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Object
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Object
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

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
     * Set updatedBy
     *
     * @param Armd\UserBundle\Entity\User $updatedBy
     * @return Object
     */
    public function setUpdatedBy(\Armd\UserBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

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
     * Hook on pre-persist operations
     *
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        //$this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
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
     * Set isOfficial
     *
     * @param boolean $isOfficial
     * @return Object
     */
    public function setIsOfficial($isOfficial)
    {
        $this->isOfficial = $isOfficial;

        return $this;
    }

    /**
     * Get isOfficial
     *
     * @return boolean
     */
    public function getIsOfficial()
    {
        return $this->isOfficial;
    }

    /**
     * Set status
     *
     * @param Armd\AtlasBundle\Entity\ObjectStatus $status
     * @return Object
     */
    public function setStatus(\Armd\AtlasBundle\Entity\ObjectStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return Armd\AtlasBundle\Entity\ObjectStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set reason
     *
     * @param string $reason
     * @return Object
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return string|null
     */
    public function getSeoTitle()
    {
        return $this->seoTitle;
    }

    /**
     * @param string $seoTitle
     * @return Object
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSeoDescription()
    {
        return $this->seoDescription;
    }

    /**
     * @param string $seoDescription
     * @return Object
     */
    public function setSeoDescription($seoDescription)
    {
        $this->seoDescription = $seoDescription;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSeoKeywords()
    {
        return $this->seoKeywords;
    }

    /**
     * @param string $seoKeywords
     * @return Object
     */
    public function setSeoKeywords($seoKeywords)
    {
        $this->seoKeywords = $seoKeywords;

        return $this;
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
        return 'armd_atlas_object';
    }

    /**
     * @return int
     */
    public function getTaggableId()
    {
        return $this->getId();
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

    public function getTouristCluster()
    {
        return $this->touristCluster;
    }

    public function setTouristCluster(TouristCluster $touristCluster = null)
    {
        $this->touristCluster = $touristCluster;

        return $this;
    }

    /**
     * Set corrected
     *
     * @param boolean $corrected
     * @return Object
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
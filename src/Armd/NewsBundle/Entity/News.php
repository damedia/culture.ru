<?php

namespace Armd\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use FOS\CommentBundle\Model\ThreadInterface;
use Armd\NewsBundle\Model\News as BaseNews;
use Armd\MkCommentBundle\Model\CommentableInterface;
use Armd\MkCommentBundle\Entity\Thread;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity(repositoryClass="Armd\NewsBundle\Repository\NewsRepository")
 * @ORM\Table(name="content_news")
 * @ORM\HasLifecycleCallbacks
 */
class News extends BaseNews implements CommentableInterface, Taggable
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
     * Date of article
     *
     * @ORM\Column(type="datetime", name="news_date", nullable=false)
     */
    protected $newsDate;

    /**
     * Start date of event
     * @ORM\Column(type="datetime", name="date_from", nullable=true)
     */
    protected $date;

    /**
     * End date of event
     * @ORM\Column(type="datetime", name="date_to", nullable=true)
     */
    protected $endDate;

    /**
     * @ORM\Column(type="datetime", name="publish_from_date", nullable=true)
     */
    protected $publishFromDate;

    /**
     * @ORM\Column(type="datetime", name="publish_to_date", nullable=true)
     */
    protected $publishToDate;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $announce;

    /**
     * @ORM\Column(type="text")
     */
    protected $body;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $source;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\OrderBy({"title"="ASC"})
     * @ORM\JoinColumn(nullable=false)
     **/
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\MainBundle\Entity\Subject")
     * @ORM\OrderBy({"title"="ASC"})
     **/
    protected $subject;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $important;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $priority = 0;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $published = true;

    /**
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    protected $publishedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $month;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $day;

    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    private $gallery;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"})
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id", nullable=true)
     */
    private $video;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="media_video_id", referencedColumnName="id", nullable=true)
     */
    private $mediaVideo;

    /**
     * @ORM\Column(name="is_on_map", type="boolean", nullable=false)
     */
    private $isOnMap = false;

    /**
     * @ORM\Column(name="lat", type="decimal", precision=15, scale=10, nullable=true)
     */
    private $lat;

    /**
     * @ORM\Column(name="lon", type="decimal", precision=15, scale=10, nullable=true)
     */
    private $lon;

    /**
     * Thread of this comment
     *
     * @var \Armd\MkCommentBundle\Entity\Thread
     * @ORM\ManyToOne(targetEntity="Armd\MkCommentBundle\Entity\Thread", cascade={"all"}, fetch="EAGER")
     */
    protected $thread;

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
     * @ORM\ManyToOne(targetEntity="Armd\NewsBundle\Entity\Theme")
     */
    protected $theme;

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
     * @ORM\JoinTable(name="content_news_stuff")
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stuff;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\AddressBundle\Entity\CountryDistrict")
     */
    protected $countryDistrict;

    public function __construct()
    {
        $this->newsDate = new \DateTime();
        $this->tags = new ArrayCollection();
        $this->stuff = new ArrayCollection();
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

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setDateParts()
    {
        $this->day = $this->getNewsDate()->format('d');
        $this->month = $this->getNewsDate()->format('m');
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setPublicationDate()
    {
        if ($this->published) {
            $this->publishedAt = new \DateTime();
        }
    }


    /**
     * Set announce
     *
     * @param text $announce
     * @return News
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;

        return $this;
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
     * @return News
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
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
     * Set category
     *
     * @param Armd\NewsBundle\Entity\Category $category
     * @return News
     */
    public function setCategory(\Armd\NewsBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return Armd\NewsBundle\Entity\Categoty
     */
    public function getCategory()
    {
        return $this->category;
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
     * @return News
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

    public function getNewsDate()
    {
        return $this->newsDate;
    }

    public function setNewsDate($newsDate)
    {
        $this->newsDate = $newsDate;

        return $this;
    }

    /**
     * Set important
     *
     * @param boolean $important
     * @return News
     */
    public function setImportant($important)
    {
        $this->important = $important;

        return $this;
    }

    /**
     * Get important
     *
     * @return boolean
     */
    public function getImportant()
    {
        return $this->important;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return News
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
     * Set source
     *
     * @param string $source
     * @return News
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set image
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $image
     * @return News
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
     * @return \Armd\TvigleVideoBundle\Entity\TvigleVideo|null
     */
    public function getVideo()
    {
        return $this->video;
    }


    /**
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $video
     * @return News
     */
    public function setVideo(\Armd\TvigleVideoBundle\Entity\TvigleVideo $video = null)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @return Media|null
     */
    public function getMediaVideo()
    {
        return $this->mediaVideo;
    }

    /**
     * @param $mediaVideo Media
     * @return News
     */
    public function setMediaVideo(Media $mediaVideo = null)
    {
        $this->mediaVideo = $mediaVideo;

        return $this;
    }


    /**
     * Set gallery
     *
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $gallery
     * @return News
     */
    public function setGallery(\Application\Sonata\MediaBundle\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;

        return $this;
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

    /**
     * Set endDate
     *
     * @param datetime $endDate
     * @return News
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return datetime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getPublishToDate()
    {
        return $this->publishToDate;
    }

    public function setPublishToDate($publishToDate)
    {
        $this->publishToDate = $publishToDate;

        return $this;
    }

    public function getPublishFromDate()
    {
        return $this->publishFromDate;
    }

    public function setPublishFromDate($publishFromDate)
    {
        $this->publishFromDate = $publishFromDate;

        return $this;
    }


    /**
     * Set priority
     *
     * @param integer $priority
     * @return News
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set date
     *
     * @param datetime $date
     * @return News
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return datetime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set month
     *
     * @param integer $month
     * @return News
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get month
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set day
     *
     * @param integer $day
     * @return News
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return integer
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set thread
     *
     * @param \Armd\MkCommentBundle\Entity\Thread $thread
     * @return News
     */
    public function setThread(Thread $thread = null)
    {
        $this->thread = $thread;

        return $this;
    }

    /**
     * Get thread
     *
     * @return \Armd\MkCommentBundle\Entity\Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     * @return News
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Set subject
     *
     * @param Armd\MainBundle\Entity\Subject $subject
     * @return News
     */
    public function setSubject(\Armd\MainBundle\Entity\Subject $subject = null)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return Armd\MainBundle\Entity\Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return News
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
     * @return News
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
     * Set isOnMap
     *
     * @param boolean $isOnMap
     * @return News
     */
    public function setIsOnMap($isOnMap)
    {
        $this->isOnMap = $isOnMap;

        return $this;
    }

    /**
     * Get isOnMap
     *
     * @return boolean
     */
    public function getIsOnMap()
    {
        return $this->isOnMap;
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
     * Set isPromoted
     *
     * @param boolean $isPromoted
     * @return News
     */
    public function setIsPromoted($isPromoted)
    {
        $this->isPromoted = $isPromoted;

        return $this;
    }

    /**
     * Get isPromoted
     *
     * @return boolean
     */
    public function getIsPromoted()
    {
        return $this->isPromoted;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        $this->tags = $this->tags ?: new ArrayCollection();

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
        return 'armd_news';
    }

    /**
     * @return int
     */
    public function getTaggableId()
    {
        return $this->getId();
    }

    /**
     * Set theme
     *
     * @param \Armd\NewsBundle\Entity\Theme $theme
     * @return News
     */
    public function setTheme(\Armd\NewsBundle\Entity\Theme $theme = null)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return \Armd\NewsBundle\Entity\Theme
     */
    public function getTheme()
    {
        return $this->theme;
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
     * Set countryDistrict
     *
     * @param \Armd\AddressBundle\Entity\CountryDistrict $countryDistrict
     * @return News
     */
    public function setCountryDistrict(\Armd\AddressBundle\Entity\CountryDistrict $countryDistrict = null)
    {
        $this->countryDistrict = $countryDistrict;

        return $this;
    }

    /**
     * Get countryDistrict
     *
     * @return \Armd\AddressBundle\Entity\CountryDistrict
     */
    public function getCountryDistrict()
    {
        return $this->countryDistrict;
    }
}
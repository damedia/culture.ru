<?php

namespace Armd\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Armd\NewsBundle\Model\News as BaseNews;
use Armd\MkCommentBundle\Model\CommentableInterface;
use Armd\MkCommentBundle\Entity\Thread;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Armd\MainBundle\Model\ChangeHistorySavableInterface;
use Damedia\SpecialProjectBundle\Entity\Page;
use Armd\TvigleVideoBundle\Entity\TvigleVideo;
use Application\Sonata\MediaBundle\Entity\Gallery;
use Armd\MainBundle\Entity\Subject;
use Armd\NewsBundle\Entity\Theme;
use Armd\AddressBundle\Entity\CountryDistrict;

/**
 *
 * @ORM\Entity(repositoryClass="\Armd\NewsBundle\Repository\NewsRepository")
 * @ORM\Table(name="content_news")
 * @ORM\HasLifecycleCallbacks
 */
class News extends BaseNews implements CommentableInterface, Taggable, ChangeHistorySavableInterface {
    /**
     * News ID
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * News title
     *
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * Date when this news occurred
     *
     * @ORM\Column(type="datetime", name="news_date", nullable=false)
     */
    protected $newsDate;

    /**
     * Start date of this news
     *
     * @ORM\Column(type="datetime", name="date_from", nullable=true)
     */
    protected $date;

    /**
     * End date of this news
     *
     * @ORM\Column(type="datetime", name="date_to", nullable=true)
     */
    protected $endDate;

    /**
     * If this news is published
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $published = true;

    /**
     * News publication start date
     *
     * @ORM\Column(type="datetime", name="publish_from_date", nullable=true)
     */
    protected $publishFromDate;

    /**
     * News publication end date
     *
     * @ORM\Column(type="datetime", name="publish_to_date", nullable=true)
     */
    protected $publishToDate;

    /**
     * A brief description of this news
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $announce;

    /**
     * A full description of this news
     *
     * @ORM\Column(type="text")
     */
    protected $body;

    /**
     * Source of information about this news
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $source;

    /**
     * News category
     *
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\OrderBy({"title"="ASC"})
     * @ORM\JoinColumn(nullable=false)
     **/
    protected $category;

    /**
     * ?????????????????????
     *
     * @ORM\ManyToOne(targetEntity="\Armd\MainBundle\Entity\Subject")
     * @ORM\OrderBy({"title"="ASC"})
     **/
    protected $subject;

    /**
     * ?????????????????????
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $important;

    /**
     * ?????????????????????
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $priority = 0;

    /**
     * ?????????????????????
     *
     * @ORM\Column(name="published_at", type="datetime", nullable=true)
     */
    protected $publishedAt;

    /**
     * ?????????????????????
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $month;

    /**
     * ?????????????????????
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $day;

    /**
     * This news main image
     *
     * @var Media
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * This news gallery
     *
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    private $gallery;

    /**
     * ?????????????????????
     *
     * @ORM\ManyToOne(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"})
     * @ORM\JoinColumn(name="video_id", referencedColumnName="id", nullable=true)
     */
    private $video;

    /**
     * This news video
     *
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="media_video_id", referencedColumnName="id", nullable=true)
     */
    private $mediaVideo;

    /**
     * If this news is represented on Atlas map
     *
     * @ORM\Column(name="is_on_map", type="boolean", nullable=false)
     */
    private $isOnMap = false;

    /**
     * Atlas map latitude
     *
     * @ORM\Column(name="lat", type="decimal", precision=15, scale=10, nullable=true)
     */
    private $lat;

    /**
     * Atlas map longitude
     *
     * @ORM\Column(name="lon", type="decimal", precision=15, scale=10, nullable=true)
     */
    private $lon;

    /**
     * This entity comments thread
     *
     * @var \Armd\MkCommentBundle\Entity\Thread
     * @ORM\ManyToOne(targetEntity="\Armd\MkCommentBundle\Entity\Thread", cascade={"all"}, fetch="EAGER")
     */
    protected $thread;

    /**
     * SEO title
     *
     * @ORM\Column(name="seo_title", type="string", nullable=true)
     */
    private $seoTitle;

    /**
     * SEO description
     *
     * @ORM\Column(name="seo_description", type="text", nullable=true)
     */
    private $seoDescription;

    /**
     * SEO keywords
     *
     * @ORM\Column(name="seo_keywords", type="text", nullable=true)
     */
    private $seoKeywords;

    /**
     * This news tags
     */
    private $tags;

    /**
     * Atlas map object theme for this news
     *
     * @ORM\ManyToOne(targetEntity="\Armd\NewsBundle\Entity\Theme")
     */
    protected $theme;

    /**
     * NOT PRESENTED!!!
     *
     * @ORM\Column(name="show_on_main", type="boolean", nullable=false)
     */
    private $showOnMain = false;

    /**
     * NOT PRESENTED!!!
     *
     * @ORM\Column(name="show_on_main_ord", type="integer", nullable=false)
     */
    private $showOnMainOrd = 0;

    /**
     * This news additional files
     *
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinTable(name="content_news_stuff")
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stuff;

    /**
     * ?????????????????????
     *
     * @ORM\ManyToOne(targetEntity="\Armd\AddressBundle\Entity\CountryDistrict")
     */
    protected $countryDistrict;

    /**
     * ?????????????????????
     *
     * @ORM\Column(name="corrected", type="boolean", nullable=true)
     */
    protected $corrected;

    /**
     * Linked Special Projects entities to this news
     *
     * @ORM\ManyToMany(targetEntity="\Damedia\SpecialProjectBundle\Entity\Page", mappedBy="news")
     */
    private $projects;



    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setDateParts() {
        $this->day = $this->getNewsDate()->format('d');
        $this->month = $this->getNewsDate()->format('m');
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setPublicationDate() {
        if ($this->published) {
            $this->publishedAt = new \DateTime();
        }
    }



    public function __construct() {
        $this->newsDate = new \DateTime();
        $this->tags = new ArrayCollection();
        $this->stuff = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }



    /**
     * overrides BaseList
     */
    public function getId() {
        return $this->id;
    }

    /**
     * implements Taggable interface
     */
    public function setTags($tags) {
        $this->tags = $tags;

        return $this;
    }
    public function getTags() {
        if (!$this->tags) {
            $this->tags = new ArrayCollection();
        }

        return $this->tags;
    }

    public function getTaggableType() {
        return 'armd_news';
    }

    public function getTaggableId() {
        return $this->getId();
    }

    /**
     * implements Commentable interface
     */
    public function setThread(Thread $thread = null) {
        $this->thread = $thread;

        return $this;
    }
    public function getThread() {
        return $this->thread;
    }

    /**
     * implements News interface
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }
    public function getTitle() {
        return $this->title;
    }

    public function setDate(\DateTime $date = null) {
        $this->date = $date;

        return $this;
    }
    public function getDate() {
        return $this->date;
    }

    /**
     * implements ChangeHistorySavable interface
     */
    public function getClassName() {
        return get_class($this);
    }


    public function setStuff($stuff) {
        $this->stuff = $stuff;
    }
    public function getStuff() {
        return $this->stuff;
    }
    public function addStuff(Media $stuff) {
        $this->stuff->add($stuff);
    }
    public function removeStuff(Media $stuff) {
        $this->stuff->removeElement($stuff);
    }

    public function setAnnounce($announce) {
        $this->announce = $announce;

        return $this;
    }
    public function getAnnounce() {
        return $this->announce;
    }

    public function setBody($body) {
        $this->body = $body;

        return $this;
    }
    public function getBody() {
        return $this->body;
    }

    public function setCategory(Category $category = null) {
        $this->category = $category;

        return $this;
    }
    public function getCategory() {
        return $this->category;
    }

    public function setNewsDate(\DateTime $newsDate) {
        $this->newsDate = $newsDate;

        return $this;
    }
    public function getNewsDate() {
        return $this->newsDate;
    }

    public function setImportant($important) {
        $this->important = $important;

        return $this;
    }
    public function getImportant() {
        return $this->important;
    }

    public function setPublished($published) {
        $this->published = $published;

        return $this;
    }
    public function getPublished() {
        return $this->published;
    }

    public function setSource($source) {
        $this->source = $source;

        return $this;
    }
    public function getSource() {
        return $this->source;
    }

    public function setImage(Media $image = null) {
        $this->image = $image;

        return $this;
    }
    public function getImage() {
        return $this->image;
    }

    public function setVideo(TvigleVideo $video = null) {
        $this->video = $video;

        return $this;
    }
    public function getVideo() {
        return $this->video;
    }

    public function setMediaVideo(Media $mediaVideo = null) {
        $this->mediaVideo = $mediaVideo;

        return $this;
    }
    public function getMediaVideo() {
        return $this->mediaVideo;
    }

    public function setGallery(Gallery $gallery = null) {
        $this->gallery = $gallery;

        return $this;
    }
    public function getGallery() {
        return $this->gallery;
    }

    public function setEndDate(\DateTime $endDate = null) {
        $this->endDate = $endDate;

        return $this;
    }
    public function getEndDate() {
        return $this->endDate;
    }

    public function setPublishToDate(\DateTime $publishToDate = null) {
        $this->publishToDate = $publishToDate;

        return $this;
    }
    public function getPublishToDate() {
        return $this->publishToDate;
    }

    public function setPublishFromDate(\DateTime $publishFromDate = null) {
        $this->publishFromDate = $publishFromDate;

        return $this;
    }
    public function getPublishFromDate() {
        return $this->publishFromDate;
    }

    public function setPriority($priority) {
        $this->priority = $priority;

        return $this;
    }
    public function getPriority() {
        return $this->priority;
    }

    public function setMonth($month) {
        $this->month = $month;

        return $this;
    }
    public function getMonth() {
        return $this->month;
    }

    public function setDay($day) {
        $this->day = $day;

        return $this;
    }
    public function getDay() {
        return $this->day;
    }

    public function setPublishedAt(\DateTime $publishedAt) {
        $this->publishedAt = $publishedAt;

        return $this;
    }
    public function getPublishedAt() {
        return $this->publishedAt;
    }

    public function setSubject(Subject $subject = null) {
        $this->subject = $subject;

        return $this;
    }
    public function getSubject() {
        return $this->subject;
    }

    public function setLat($lat) {
        $this->lat = $lat;

        return $this;
    }
    public function getLat() {
        return $this->lat;
    }

    public function setLon($lon) {
        $this->lon = $lon;

        return $this;
    }
    public function getLon() {
        return $this->lon;
    }

    public function setIsOnMap($isOnMap) {
        $this->isOnMap = $isOnMap;

        return $this;
    }
    public function getIsOnMap() {
        return $this->isOnMap;
    }

    public function setSeoTitle($seoTitle) {
        $this->seoTitle = $seoTitle;

        return $this;
    }
    public function getSeoTitle() {
        return $this->seoTitle;
    }

    public function setSeoDescription($seoDescription) {
        $this->seoDescription = $seoDescription;

        return $this;
    }
    public function getSeoDescription() {
        return $this->seoDescription;
    }

    public function setSeoKeywords($seoKeywords) {
        $this->seoKeywords = $seoKeywords;

        return $this;
    }
    public function getSeoKeywords() {
        return $this->seoKeywords;
    }

    public function setTheme(Theme $theme = null) {
        $this->theme = $theme;

        return $this;
    }
    public function getTheme() {
        return $this->theme;
    }

    public function setShowOnMain($showOnMain) {
        $this->showOnMain = $showOnMain;

        return $this;
    }
    public function getShowOnMain() {
        return $this->showOnMain;
    }

    public function setShowOnMainOrd($showOnMainOrd) {
        $this->showOnMainOrd = $showOnMainOrd;

        return $this;
    }
    public function getShowOnMainOrd() {
        return $this->showOnMainOrd;
    }

    public function setCountryDistrict(CountryDistrict $countryDistrict = null) {
        $this->countryDistrict = $countryDistrict;

        return $this;
    }
    public function getCountryDistrict() {
        return $this->countryDistrict;
    }

    public function setCorrected($corrected) {
        $this->corrected = $corrected;

        return $this;
    }
    public function getCorrected() {
        return $this->corrected;
    }

    public function setProjects(Page $page) {
        $this->projects[] = $page;

        return $this;
    }
    public function getProjects() {
        return $this->projects;
    }

    public function addProjects(Page $page) {
        $this->projects[] = $page;

        return $this;
    }
    public function removeProjects(Page $page) {
        $this->projects->removeElement($page);
    }
}
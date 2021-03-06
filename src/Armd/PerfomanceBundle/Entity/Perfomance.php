<?php

namespace Armd\PerfomanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;
use Armd\MainBundle\Model\ChangeHistorySavableInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="content_perfomance")
 * @ORM\Entity(repositoryClass="Armd\PerfomanceBundle\Repository\PerfomanceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Perfomance implements Taggable, ChangeHistorySavableInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published = true;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="perfomance_video_id", referencedColumnName="id")
     */
    private $perfomanceVideo;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="trailer_video_id", referencedColumnName="id", nullable=true)
     */
    private $trailerVideo;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="perfomance_media_video_id", referencedColumnName="id")
     */
    private $mediaPerfomanceVideo;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="trailer_media_video_id", referencedColumnName="id", nullable=true)
     */
    private $mediaTrailerVideo;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="\Armd\PerfomanceBundle\Entity\PerfomanceGanre", inversedBy="perfomances")
     * @ORM\JoinTable(name="content_perfomance_perfomance_ganre")
     */
    private $ganres;

    /**
     * @ORM\Column(name="view_count", type="integer", nullable=true)
     */
    private $viewCount = 0;

    private $tags;

    /**
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="interview_video_id", referencedColumnName="id", nullable=true)
     */
    private $interviewVideo;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="interview_media_video_id", referencedColumnName="id", nullable=true)
     */
    private $mediaInterviewVideo;

    /**
     * @ORM\Column(name="interview_title", type="string", length=255, nullable=true)
     */
    private $interviewTitle;

    /**
     * @ORM\Column(name="interview_description", type="text", nullable=true)
     */
    private $interviewDescription;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    private $gallery;

    /**
     *
     * @ORM\Column(name="external_url", type="string", nullable=true)
     */
    private $externalUrl;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\TheaterBundle\Entity\Theater")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id")
     */
    private $theater;

    /**
     * @ORM\Column(name="corrected", type="boolean", nullable=true)
     */
    protected $corrected;

    /**
     * @ORM\Column(name="show_on_main", type="boolean", nullable=true)
     */
    private $showOnMain = false;

    /**
     * @ORM\Column(name="show_on_main_from", type="datetime", nullable=true)
     */
    private $showOnMainFrom;

    /**
     * @ORM\Column(name="show_on_main_to", type="datetime", nullable=true)
     */
    private $showOnMainTo;

    /**
     * @Assert\Type(type="integer", message="Целое число - порядковый номер, чем больше, тем хуже приоритет")
     * @ORM\Column(name="show_on_main_ord", type="integer", nullable=true)
     */
    private $showOnMainOrd;


    /** Additional fields form carousel on main page */

    /**
     * Длительность спектакля в минутах, например 120
     * @Assert\Type(type="integer", message="Длительность спектакля в минутах, например 120.")
     * @ORM\Column(name="time_length", type="integer", nullable=true)
     */
    private $timeLength;

    /**
     * @Assert\Length(max = "255")
     * @ORM\Column(name="stars", type="string", length=255, nullable=true)
     */
    protected $stars;


    /**
     * @return int
     */
    public function getTaggableId()
    {
        return $this->getId();
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
    }

    /**
     * @return string
     */
    public function getTaggableType()
    {
        return 'armd_perfomance';
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
     * @return Perfomance
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Perfomance
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
     * Set published
     *
     * @param boolean $published
     * @return Perfomance
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
     * Set description
     *
     * @param string $description
     * @return Perfomance
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
     * Set perfomanceVideo
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $perfomanceVideo
     * @return Perfomance
     */
    public function setPerfomanceVideo(\Armd\TvigleVideoBundle\Entity\TvigleVideo $perfomanceVideo = null)
    {
        $this->perfomanceVideo = $perfomanceVideo;

        return $this;
    }

    /**
     * Get perfomanceVideo
     *
     * @return \Armd\TvigleVideoBundle\Entity\TvigleVideo
     */
    public function getPerfomanceVideo()
    {
        return $this->perfomanceVideo;
    }

    /**
     * Set trailerVideo
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $trailerVideo
     * @return Perfomance
     */
    public function setTrailerVideo(\Armd\TvigleVideoBundle\Entity\TvigleVideo $trailerVideo = null)
    {
        $this->trailerVideo = $trailerVideo;

        return $this;
    }

    /**
     * Get trailerVideo
     *
     * @return \Armd\TvigleVideoBundle\Entity\TvigleVideo
     */
    public function getTrailerVideo()
    {
        return $this->trailerVideo;
    }

    /**
     * Set mediaPerfomanceVideo
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $mediaPerfomanceVideo
     * @return Perfomance
     */
    public function setMediaPerfomanceVideo(Media $mediaPerfomanceVideo = null)
    {
        $this->mediaPerfomanceVideo = $mediaPerfomanceVideo;

        return $this;
    }

    /**
     * Get mediaPerfomanceVideo
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getMediaPerfomanceVideo()
    {
        return $this->mediaPerfomanceVideo;
    }

    /**
     * Set mediaTrailerVideo
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $mediaTrailerVideo
     * @return Perfomance
     */
    public function setMediaTrailerVideo(Media $mediaTrailerVideo = null)
    {
        $this->mediaTrailerVideo = $mediaTrailerVideo;

        return $this;
    }

    /**
     * Get mediaTrailerVideo
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getMediaTrailerVideo()
    {
        return $this->mediaTrailerVideo;
    }


    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ganres = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set viewCount
     *
     * @param integer $viewCount
     * @return Perfomance
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * Get viewCount
     *
     * @return integer
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * Add ganres
     *
     * @param \Armd\PerfomanceBundle\Entity\PerfomanceGanre $ganres
     * @return Perfomance
     */
    public function addGanre(\Armd\PerfomanceBundle\Entity\PerfomanceGanre $ganres)
    {
        $this->ganres[] = $ganres;

        return $this;
    }

    /**
     * Remove ganres
     *
     * @param \Armd\PerfomanceBundle\Entity\PerfomanceGanre $ganres
     */
    public function removeGanre(\Armd\PerfomanceBundle\Entity\PerfomanceGanre $ganres)
    {
        $this->ganres->removeElement($ganres);
    }

    /**
     * Get ganres
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGanres()
    {
        return $this->ganres;
    }

    public function addViewCount()
    {
        $this->viewCount++;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return Perfomance
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set interviewTitle
     *
     * @param string $interviewTitle
     * @return Perfomance
     */
    public function setInterviewTitle($interviewTitle)
    {
        $this->interviewTitle = $interviewTitle;

        return $this;
    }

    /**
     * Get interviewTitle
     *
     * @return string
     */
    public function getInterviewTitle()
    {
        return $this->interviewTitle;
    }

    /**
     * Set interviewVideo
     *
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $interviewVideo
     * @return Perfomance
     */
    public function setInterviewVideo(\Armd\TvigleVideoBundle\Entity\TvigleVideo $interviewVideo = null)
    {
        $this->interviewVideo = $interviewVideo;

        return $this;
    }

    /**
     * Get interviewVideo
     *
     * @return \Armd\TvigleVideoBundle\Entity\TvigleVideo
     */
    public function getInterviewVideo()
    {
        return $this->interviewVideo;
    }

     /**
     * Set mediaInterviewVideo
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $mediaInterviewVideo
     * @return Perfomance
     */
    public function setMediaInterviewVideo(Media $mediaInterviewVideo = null)
    {
        $this->mediaInterviewVideo = $mediaInterviewVideo;

        return $this;
    }

    /**
     * Get mediaInterviewVideo
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getMediaInterviewVideo()
    {
        return $this->mediaInterviewVideo;
    }

    /**
     * Set gallery
     *
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $gallery
     * @return Perfomance
     */
    public function setGallery(\Application\Sonata\MediaBundle\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;

        return $this;
    }

    /**
     * Get gallery
     *
     * @return \Application\Sonata\MediaBundle\Entity\Gallery
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set externalUrl
     *
     * @param string $externalUrl
     * @return Perfomance
     */
    public function setExternalUrl($externalUrl)
    {
        $this->externalUrl = $externalUrl;

        return $this;
    }

    /**
     * Get externalUrl
     *
     * @return string
     */
    public function getExternalUrl()
    {
        return $this->externalUrl;
    }

    /**
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return Perfomance
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
     * Set interviewDescription
     *
     * @param string $interviewDescription
     * @return Perfomance
     */
    public function setInterviewDescription($interviewDescription)
    {
        $this->interviewDescription = $interviewDescription;

        return $this;
    }

    /**
     * Get interviewDescription
     *
     * @return string
     */
    public function getInterviewDescription()
    {
        return $this->interviewDescription;
    }

    /**
     * Set theater
     *
     * @param \Armd\TheaterBundle\Entity\Theater $theater
     * @return Perfomance
     */
    public function setTheater(\Armd\TheaterBundle\Entity\Theater $theater = null)
    {
        $this->theater = $theater;

        return $this;
    }

    /**
     * Get theater
     *
     * @return \Armd\TheaterBundle\Entity\Theater
     */
    public function getTheater()
    {
        return $this->theater;
    }

    /**
     * Set corrected
     *
     * @param boolean $corrected
     * @return Perfomance
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

    /**
     * @return boolean
     */
    public function getShowOnMain()
    {
        return $this->showOnMain;
    }

    /**
     * @param $showOnMain boolean
     * @return $this
     */
    public function setShowOnMain($showOnMain)
    {
        $this->showOnMain = $showOnMain;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShowOnMainFrom()
    {
        return $this->showOnMainFrom;
    }

    /**
     * @param $showOnMainFrom \DateTime
     * @return $this
     */
    public function setShowOnMainFrom($showOnMainFrom)
    {
        $this->showOnMainFrom = $showOnMainFrom;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShowOnMainTo()
    {
        return $this->showOnMainTo;
    }

    /**
     * @param $showOnMainTo \DateTime
     * @return $this
     */
    public function setShowOnMainTo($showOnMainTo)
    {
        $this->showOnMainTo = $showOnMainTo;

        return $this;
    }

    /**
     * @param $timeLength
     * @return Perfomance
     */
    public function setTimeLength($timeLength)
    {
        $this->timeLength = $timeLength;

        return $this;
    }

    /**
     * @return integer|null
     */
    public function getTimeLength()
    {
        return $this->timeLength;
    }

    /**
     * @param $stars
     * @return Perfomance
     */
    public function setStars($stars)
    {
        $this->stars = $stars;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * @return integer
     */
    public function getShowOnMainOrd()
    {
        return $this->showOnMainOrd;
    }

    /**
     * @param $showOnMainOrd
     * @return Perfomance
     */
    public function setShowOnMainOrd($showOnMainOrd)
    {
        $this->showOnMainOrd = $showOnMainOrd;

        return $this;
    }

    /**
     * @return string
     */
    public function getTimeLengthInHours()
    {
        if ($this->timeLength > 0) {
            return str_pad(floor($this->timeLength / 60), 2, '0', STR_PAD_LEFT) . ':' . str_pad(($this->timeLength % 60), 2, '0', STR_PAD_LEFT);
        }
        return '';
    }


    public function getClassName()
    {
        return get_class($this);
    }
}
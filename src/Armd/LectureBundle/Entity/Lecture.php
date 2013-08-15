<?php

namespace Armd\LectureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Application\Sonata\MediaBundle\Entity\Media;
use Application\Sonata\MediaBundle\Entity\Gallery;
use Doctrine\Common\Collections\ArrayCollection;
use Armd\MainBundle\Model\ChangeHistorySavableInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="lecture")
 * @ORM\Entity(repositoryClass="\Armd\LectureBundle\Repository\LectureRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Lecture implements Taggable, ChangeHistorySavableInterface
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
     * @ORM\Column(name="announce", type="text", nullable=true)
     */
    private $announce;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="lecturer", type="string", length=255, nullable=true)
     */
    private $lecturer;

    /**
     * @ORM\Column(name="recommended", type="boolean", nullable=true)
     */
    private $recommended = false;

    /**
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published = true;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="lecture_video_id", referencedColumnName="id", nullable=true)
     */
    private $lectureVideo;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="trailer_video_id", referencedColumnName="id", nullable=true)
     */
    private $trailerVideo;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="media_lecture_video_id", referencedColumnName="id", nullable=true)
     */
    private $mediaLectureVideo;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="media_trailer_video_id", referencedColumnName="id", nullable=true)
     */
    private $mediaTrailerVideo;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery", cascade={"persist"}, fetch="EAGER")
     */
    private $series;


    /**
     * @ORM\ManyToOne(targetEntity="LectureSuperType", fetch="EAGER")
     * @ORM\JoinColumn(name="lecture_super_type_id", referencedColumnName="id", nullable=true)
     */
    private $lectureSuperType;


    /**
     * @ORM\ManyToMany(targetEntity="LectureCategory", inversedBy="lectures")
     * @ORM\JoinTable(name="lecture_category_lecture")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="LectureGenre")
     * @ORM\JoinTable(name="lecture_genre_lecture")
     */
    private $genres;

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
     * Длительность фильма в минутах, например 120
     *@Assert\Type(type="integer", message="Длительность фильма в минутах, например 120.")
     * @ORM\Column(name="time_length", type="integer", nullable=true)
     */
    private $timeLength;

    /**
     * Год выхода фильма, например 1969
     * @Assert\Type(type="integer", message="Год выхода фильма, например 1969.")
     * @Assert\Range(min="1900", max="2100")
     * @ORM\Column(name="production_year", type="integer", nullable=true)
     */
    private $productionYear;

    /**
     * Ссылка на внешний ресурс. Для списка лучших 100 фильмов
     *
     * @ORM\Column(name="external_url", type="string", nullable=true)
     */
    private $externalUrl;

    /**
     * @ORM\OneToMany(targetEntity="\Armd\LectureBundle\Entity\LectureRolePerson", mappedBy="lecture")
     */
    private $rolesPersons;

    /**
     * @ORM\Column(name="view_count", type="integer", nullable=true)
     */
    private $viewCount = 0;

    /**
     * @ORM\Column(name="is_top_100_film", type="boolean", nullable=false)
     */
    private $isTop100Film = false;

    /**
     * @ORM\Column(name="show_on_main_as_recommended", type="boolean", nullable=true)
     */
    private $showOnMainAsRecommended = false;

    /**
     * @ORM\Column(name="show_on_main_as_recommended_from", type="datetime", nullable=true)
     */
    private $showOnMainAsRecommendedFrom;

    /**
     * @ORM\Column(name="show_on_main_as_recommended_to", type="datetime", nullable=true)
     */
    private $showOnMainAsRecommendedTo;

    /**
     * @ORM\Column(name="show_on_main_as_recommended_ord", type="integer", nullable=true)
     */
    private $showOnMainAsRecommendedOrd = 0;

    /**
     * @ORM\Column(name="show_on_main_as_for_children", type="boolean", nullable=false)
     */
    private $showOnMainAsForChildren = false;

    /**
     * @ORM\Column(name="show_on_main_as_for_children_from", type="datetime", nullable=true)
     */
    private $showOnMainAsForChildrenFrom;

    /**
     * @ORM\Column(name="show_on_main_as_for_children_to", type="datetime", nullable=true)
     */
    private $showOnMainAsForChildrenTo;

    /**
     * @ORM\Column(name="show_on_main_as_for_children_ord", type="integer", nullable=false)
     */
    private $showOnMainAsForChildrenOrd = 0;

    /**
     * @ORM\Column(name="is_headline", type="boolean", nullable=true)
     */
    private $isHeadline = false;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinTable(name="lecture_stuff")
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $stuff;


    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="vertical_banner_id", referencedColumnName="id", nullable=true)
     */
    private $verticalBanner;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="horizontal_banner_id", referencedColumnName="id", nullable=true)
     */
    private $horizontalBanner;

    /**
     * @ORM\Column(name="show_at_slider", type="boolean", nullable=true)
     */
    private $showAtSlider = false;

    /**
     * @ORM\Column(name="show_at_featured", type="boolean", nullable=true)
     */
    private $showAtFeatured = false;

    /**
     * @ORM\ManyToMany(targetEntity="LectureGenre")
     * @ORM\JoinTable(name="lecture_limit_slider_genre")
     */
    private $limitSliderForGenres;

    /**
     * @ORM\ManyToMany(targetEntity="LectureGenre")
     * @ORM\JoinTable(name="lecture_limit_featured_genre")
     */
    private $limitFeaturedForGenres;

    /**
     * @ORM\Column(name="corrected", type="boolean", nullable=true)
     */
    protected $corrected;

    /** Additional fields form carousel on main page */

    /**
     * @Assert\Length(max = "255")
     * @ORM\Column(name="director", type="string", length=255, nullable=true)
     */
    protected $director;

    /**
     * @Assert\Length(max = "255")
     * @ORM\Column(name="stars", type="string", length=255, nullable=true)
     */
    protected $stars;


    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->genres = new ArrayCollection();
        $this->rolesPersons = new ArrayCollection();
        $this->stuff = new ArrayCollection();
        $this->limitSliderForGenres = new ArrayCollection();
        $this->limitFeaturedForGenres = new ArrayCollection();
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

//    /**
//     * @ORM\PostLoad
//     */
//    public function postLoad()
//    {
//        $this->genres1 = $this->getGenres1();
//        $this->genres2 = $this->getGenres2();
//    }


    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getAnnounce()
    {
        return $this->announce;
    }

    /**
     * @param mixed $announce
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getLecturer()
    {
        return $this->lecturer;
    }

    public function setLecturer($lecturer)
    {
        $this->lecturer = $lecturer;
    }

    public function getRecommended()
    {
        return $this->recommended;
    }

    public function setRecommended($recommended)
    {
        $this->recommended = $recommended;
    }

    public function getPublished()
    {
        return $this->published;
    }

    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return \Armd\TvigleVideoBundle\Entity\TvigleVideo
     */
    public function getLectureVideo()
    {
        return $this->lectureVideo;
    }

    /**
     * @param \Armd\TvigleVideoBundle\Entity\TvigleVideo $lectureVideo
     */
    public function setLectureVideo($lectureVideo)
    {
        $this->lectureVideo = $lectureVideo;
    }

    /**
     * @return \Armd\TvigleVideoBundle\Entity\TvigleVideo
     */
    public function getTrailerVideo()
    {
        return $this->trailerVideo;
    }

    /**
     * @param $trailerVideo
     */
    public function setTrailerVideo($trailerVideo)
    {
        $this->trailerVideo = $trailerVideo;
    }

    /**
     * @return Media|null
     */
    public function getMediaLectureVideo()
    {
        return $this->mediaLectureVideo;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Media $mediaLectureVideo
     */
    public function setMediaLectureVideo(Media $mediaLectureVideo = null)
    {
        $this->mediaLectureVideo = $mediaLectureVideo;
    }

    /**
     * @return Media|null
     */
    public function getMediaTrailerVideo()
    {
        return $this->mediaTrailerVideo;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Media $mediaTrailerVideo
     */
    public function setMediaTrailerVideo(Media $mediaTrailerVideo = null)
    {
        $this->mediaTrailerVideo = $mediaTrailerVideo;
    }

    /**
     * @return Gallery|null
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Gallery $series
     */
    public function setSeries(Gallery $series = null)
    {
        $this->series = $series;
    }

    /**
     * @return \Armd\LectureBundle\Entity\LectureSuperType
     */
    public function getLectureSuperType()
    {
        return $this->lectureSuperType;
    }

    /**
     * @param LectureSuperType $lectureSuperType
     * @return \Armd\LectureBundle\Entity\Lecture
     */
    public function setLectureSuperType(LectureSuperType $lectureSuperType)
    {
        $this->lectureSuperType = $lectureSuperType;
        return $this;
    }


    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    public function addCategory(\Armd\LectureBundle\Entity\LectureCategory $category)
    {
        $this->categories[] = $category;
    }

    public function removeCategory(\Armd\LectureBundle\Entity\LectureCategory $category)
    {
        $this->categories->removeCategory($category);
    }

    public function getGenres()
    {
        return $this->genres;
    }

    public function setGenres($genres)
    {
        $this->genres = $genres;

        return $this;
    }

    public function addGenre(LectureGenre $genre)
    {
        if ($this->genres->indexOf($genre) === false) {
            $this->genres->add($genre);
        }
    }

    public function removeGenre(LectureGenre $genre)
    {
        $this->genres->removeElement($genre);
    }

    public function setGenres1($genres) {
        $this->setGenresByLevel($genres, 1);
    }

    public function getGenres1() {
        return $this->getGenresByLevel(1);
    }

    public function setGenres2($genres) {
        $this->setGenresByLevel($genres, 2);
    }

    public function getGenres2() {
        return $this->getGenresByLevel(2);
    }

    public function setGenresByLevel($genres, $level) {
        // add genres
        foreach ($genres as $genre) {
            if ($genre->getLevel() == $level && !$this->genres->contains($genre)) {
                $this->genres->add($genre);
            }
        }

        // remove excess genres
        $elementsToRemove = array();
        foreach ($this->genres as $genre) {
            if ($genre->getLevel() == $level && !$genres->contains($genre)) {
                $elementsToRemove[] = $genre;
            }
        }

        foreach ($elementsToRemove as $element) {
            $this->genres->removeElement($element);
        }


    }

    public function getGenresByLevel($level) {
        $genres = new ArrayCollection();
        foreach ($this->genres as $genre) {
            if ($genre->getLevel() == $level) {
                $genres->add($genre);
            }
        }
        return $genres;
    }

    public function getGenreByLevel($level) {
        foreach ($this->genres as $genre) {
            if ($genre->getLevel() == $level) {
                return $genre;
            }
        }
        return false;
    }

    public function getFiltrableGenres() {
        if ($this->lectureSuperType->getCode() === 'LECTURE_SUPER_TYPE_CINEMA') {
            $genres = array();
            foreach ($this->getGenres() as $genre) {
                if ($genre->getLevel() === 2) {
                    $genres[] = $genre;
                }
            }
            return $genres;
        } else {
            return $this->getGenres();
        }
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
     * @return void
     */
    public function setSeoTitle($seoTitle)
    {
        $this->seoTitle = $seoTitle;
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
     * @return void
     */
    public function setSeoDescription($seoDescription)
    {
        $this->seoDescription = $seoDescription;
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
     * @return void
     */
    public function setSeoKeywords($seoKeywords)
    {
        $this->seoKeywords = $seoKeywords;
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

    /*
     * Add categories
     *
     * @param \Armd\LectureBundle\Entity\LectureCategory $categories
     * @return Lecture
     */
    public function addCategorie(\Armd\LectureBundle\Entity\LectureCategory $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \Armd\LectureBundle\Entity\LectureCategory $categories
     */
    public function removeCategorie(\Armd\LectureBundle\Entity\LectureCategory $categories)
    {
        $this->categories->removeElement($categories);
    }


    /**
     * Add rolesPersons
     *
     * @param \Armd\LectureBundle\Entity\LectureRolePerson $rolesPersons
     * @return Lecture
     */
    public function addRolesPerson(\Armd\LectureBundle\Entity\LectureRolePerson $rolesPersons)
    {
        $this->rolesPersons[] = $rolesPersons;

        return $this;
    }

    /**
     * Set rolesPersons
     *
     * @param mixed $rolesPersons
     * @return Lecture
     */
    public function setRolesPersons($rolesPersons)
    {
        if (is_array($rolesPersons) || ($rolesPersons instanceof ArrayCollection)) {
            $this->rolesPersons = $rolesPersons;
        } else {
            $this->addRolesPerson($rolesPersons);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getTaggableType()
    {
        return 'armd_lecture';
    }

    /**
     * @return int
     */
    public function getTaggableId()
    {
        return $this->getId();
    }

    /*
     * Remove rolesPersons
     *
     * @param \Armd\LectureBundle\Entity\LectureRolePerson $rolesPersons
     */
    public function removeRolesPerson(\Armd\LectureBundle\Entity\LectureRolePerson $rolesPersons)
    {
        $this->rolesPersons->removeElement($rolesPersons);
    }

    /**
     * Get rolesPersons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRolesPersons()
    {
        return $this->rolesPersons;
    }

    /**
     * Add rolesPersons
     *
     * @param \Armd\LectureBundle\Entity\LectureRolePerson $rolesPersons
     * @return Lecture
     */
    public function addRolesPersons($rolesPersons)
    {
        if (is_array($rolesPersons) || ($rolesPersons instanceof ArrayCollection)) {
            foreach ($rolesPersons as $obj) {
                $this->addRolesPerson($obj);
            }
        } else {
            $this->addRolesPerson($rolesPersons);
        }
    }

    /**
     * Set timeLength
     *
     * @param integer $timeLength
     * @return Lecture
     */
    public function setTimeLength($timeLength)
    {
        $this->timeLength = $timeLength;

        return $this;
    }

    /**
     * Get timeLength
     *
     * @return integer
     */
    public function getTimeLength()
    {
        return $this->timeLength;
    }

    /**
     * Set productionYear
     *
     * @param integer $productionYear
     * @return Lecture
     */
    public function setProductionYear($productionYear)
    {
        $this->productionYear = $productionYear;

        return $this;
    }

    /**
     * Get productionYear
     *
     * @return integer
     */
    public function getProductionYear()
    {
        return $this->productionYear;
    }

    /**
     * Set externalUrl
     *
     * @param string $externalUrl
     * @return Lecture
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

    public function getViewCount()
    {
        return $this->viewCount;
    }

    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
    }

    public function addViewCount()
    {
        $this->viewCount++;
    }

    public function getIsTop100Film()
    {
        return is_null($this->isTop100Film) ? 0 : $this->isTop100Film;
    }

    public function setIsTop100Film($isTop100Film)
    {
        $this->isTop100Film = $isTop100Film;
    }

    /**
     * @return boolean
     */
    public function getShowOnMainAsRecommended()
    {
        return $this->showOnMainAsRecommended;
    }

    /**
     * @param $showOnMainAsRecommended
     * @return $this
     */
    public function setShowOnMainAsRecommended($showOnMainAsRecommended)
    {
        $this->showOnMainAsRecommended = $showOnMainAsRecommended;

        return $this;
    }

    /**
     * @return integer
     */
    public function getShowOnMainAsRecommendedOrd()
    {
        return $this->showOnMainAsRecommendedOrd;
    }

    public function setShowOnMainAsRecommendedOrd($showOnMainAsRecommendedOrd)
    {
        $this->showOnMainAsRecommendedOrd = $showOnMainAsRecommendedOrd;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShowOnMainAsRecommendedFrom()
    {
        return $this->showOnMainAsRecommendedFrom;
    }

    /**
     * @param $showOnMainAsRecommendedFrom \DateTime
     * @return $this
     */
    public function setShowOnMainAsRecommendedFrom($showOnMainAsRecommendedFrom)
    {
        $this->showOnMainAsRecommendedFrom = $showOnMainAsRecommendedFrom;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShowOnMainAsRecommendedTo()
    {
        return $this->showOnMainAsRecommendedTo;
    }

    /**
     * @param $showOnMainAsRecommendedTo \DateTime
     * @return $this
     */
    public function setShowOnMainAsRecommendedTo($showOnMainAsRecommendedTo)
    {
        $this->showOnMainAsRecommendedTo = $showOnMainAsRecommendedTo;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getShowOnMainAsForChildren()
    {
        return $this->showOnMainAsForChildren;
    }

    /**
     * @param $showOnMainAsForChildren
     * @return $this
     */
    public function setShowOnMainAsForChildren($showOnMainAsForChildren)
    {
        $this->showOnMainAsForChildren = $showOnMainAsForChildren;

        return $this;
    }

    /**
     * @return integer
     */
    public function getShowOnMainAsForChildrenOrd()
    {
        return $this->showOnMainAsForChildrenOrd;
    }

    public function setShowOnMainAsForChildrenOrd($showOnMainAsForChildrenOrd)
    {
        $this->showOnMainAsForChildrenOrd = $showOnMainAsForChildrenOrd;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShowOnMainAsForChildrenFrom()
    {
        return $this->showOnMainAsForChildrenFrom;
    }

    /**
     * @param $showOnMainAsForChildrenFrom \DateTime
     * @return $this
     */
    public function setShowOnMainAsForChildrenFrom($showOnMainAsForChildrenFrom)
    {
        $this->showOnMainAsForChildrenFrom = $showOnMainAsForChildrenFrom;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getShowOnMainAsForChildrenTo()
    {
        return $this->showOnMainAsForChildrenTo;
    }

    /**
     * @param $showOnMainAsForChildrenTo \DateTime
     * @return $this
     */
    public function setShowOnMainAsForChildrenTo($showOnMainAsForChildrenTo)
    {
        $this->showOnMainAsForChildrenTo = $showOnMainAsForChildrenTo;

        return $this;
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
     * @param boolean $isHeadline
     */
    public function setIsHeadline($isHeadline)
    {
        $this->isHeadline = $isHeadline;
    }

    /**
     * @return boolean
     */
    public function getIsHeadline()
    {
        return $this->isHeadline;
    }

    /**
     * @return mixed
     */
    public function getVerticalBanner()
    {
        return $this->verticalBanner;
    }

    /**
     * @param mixed $verticalBanner
     */
    public function setVerticalBanner(\Application\Sonata\MediaBundle\Entity\Media $verticalBanner = null)
    {
        if (is_null($verticalBanner) || $verticalBanner->isUploaded()) {
            $this->verticalBanner = $verticalBanner;
        }
    }

    /**
     * @return mixed
     */
    public function getHorizontalBanner()
    {
        return $this->horizontalBanner;
    }

    /**
     * @param mixed $horizontalBanner
     */
    public function setHorizontalBanner(\Application\Sonata\MediaBundle\Entity\Media $horizontalBanner = null)
    {
        if (is_null($horizontalBanner) || $horizontalBanner->isUploaded()) {
            $this->horizontalBanner = $horizontalBanner;
        }
    }

    /**
     * @return mixed
     */
    public function getShowAtSlider()
    {
        return $this->showAtSlider;
    }

    /**
     * @param mixed $showAtSlider
     */
    public function setShowAtSlider($showAtSlider)
    {
        $this->showAtSlider = $showAtSlider;
    }

    /**
     * @return mixed
     */
    public function getShowAtFeatured()
    {
        return $this->showAtFeatured;
    }

    /**
     * @param mixed $showAtFeatured
     */
    public function setShowAtFeatured($showAtFeatured)
    {
        $this->showAtFeatured = $showAtFeatured;
    }

    /**
     * @return mixed
     */
    public function getLimitSliderForGenres()
    {
        return $this->limitSliderForGenres;
    }

    /**
     * @param mixed $limitSliderForGenres
     */
    public function setLimitSliderForGenres($limitSliderForGenres)
    {
        $this->limitSliderForGenres = $limitSliderForGenres;
    }

    /**
     * @return mixed
     */
    public function getLimitFeaturedForGenres()
    {
        return $this->limitFeaturedForGenres;
    }

    /**
     * @param mixed $limitFeaturedForGenres
     */
    public function setLimitFeaturedForGenres($limitFeaturedForGenres)
    {
        $this->limitFeaturedForGenres = $limitFeaturedForGenres;
    }

    /**
     * Set corrected
     *
     * @param boolean $corrected
     * @return Lecture
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
     * @param $director
     * @return Lecture
     */
    public function setDirector($director)
    {
        $this->director = $director;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDirector()
    {
        return $this->director;
    }

    /**
     * @param $stars
     * @return Lecture
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

    public function getClassName()
    {
        return get_class($this);
    }
}
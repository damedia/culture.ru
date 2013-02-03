<?php

namespace Armd\LectureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="lecture")
 * @ORM\Entity(repositoryClass="\Armd\LectureBundle\Repository\LectureRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Lecture implements Taggable
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
     * @ORM\JoinColumn(name="lecture_video_id", referencedColumnName="id")
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
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="lecture_file_id", referencedColumnName="id")
     */
    private $lectureFile;

    /**
     * @ORM\ManyToOne(targetEntity="LectureSuperType", fetch="EAGER")
     * @ORM\JoinColumn(name="lecture_super_type_id", referencedColumnName="id", nullable=true)
     */
    private $lectureSuperType;

    /**
     * @ORM\ManyToOne(targetEntity="LectureType", fetch="EAGER")
     * @ORM\JoinColumn(name="lecture_type_id", referencedColumnName="id")
     */
    private $lectureType;

    /**
     * @ORM\ManyToMany(targetEntity="LectureCategory", inversedBy="lectures")
     * @ORM\JoinTable(name="lecture_category_lecture")
     */
    private $categories;

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
     *
     * @ORM\Column(name="time_length", type="integer", nullable=true)
     */
    private $timeLength;

    /**
     * Год выхода фильма, например 1969
     *
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

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->rolesPersons = new ArrayCollection();
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
    public function setMediaTrailerVideo(Media $mediaTrailerVideo)
    {
        $this->mediaTrailerVideo = $mediaTrailerVideo;
    }

    /**
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getLectureFile()
    {
        return $this->lectureFile;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Media $lectureFile
     */
    public function setLectureFile(\Application\Sonata\MediaBundle\Entity\Media $lectureFile = null)
    {
        if (is_null($lectureFile) || $lectureFile->isUploaded()) {
            $this->lectureFile = $lectureFile;
        }
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

    /**
     * @return \Armd\LectureBundle\Entity\LectureType
     */
    public function getLectureType()
    {
        return $this->lectureType;
    }

    /**
     * @param LectureType $lectureType
     */
    public function setLectureType(\Armd\LectureBundle\Entity\LectureType $lectureType)
    {
        $this->lectureType = $lectureType;
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
}
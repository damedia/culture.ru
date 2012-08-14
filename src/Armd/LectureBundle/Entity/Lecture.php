<?php

namespace Armd\LectureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="lecture")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Lecture
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
     * @ORM\Column(name="lecturer", type="string", length=255, nullable=true)
     */
    private $lecturer;

    /**
     * @ORM\Column(name="recommended", type="boolean", nullable=true)
     */
    private $recommended = false;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\TvigleVideoBundle\Entity\TvigleVideo", cascade={"persist"})
     * @ORM\JoinColumn(name="lecture_video_id", referencedColumnName="id")
     */
    private $lectureVideo;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="lecture_file_id", referencedColumnName="id")
     */
    private $lectureFile;

    /**
     * @ORM\ManyToOne(targetEntity="LectureType")
     * @ORM\JoinColumn(name="lecture_type_id", referencedColumnName="id")
     */
    private $lectureType;

    /**
     * @ORM\ManyToMany(targetEntity="LectureCategory", inversedBy="lectures")
     * @ORM\JoinTable(name="lecture_category_lecture")
     */
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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
}
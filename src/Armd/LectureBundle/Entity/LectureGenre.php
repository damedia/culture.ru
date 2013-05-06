<?php

namespace Armd\LectureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="lecture_genre")
 */
class LectureGenre
{
    /**
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="LectureSuperType")
     * @ORM\JoinColumn(name="lecture_super_type_id", referencedColumnName="id")
     */
    private $lectureSuperType;

    /**
     * @ORM\Column(name="level", type="integer")
     */
    private $level = 1;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getLectureSuperType()
    {
        return $this->lectureSuperType;
    }

    public function setLectureSuperType($lectureSuperType)
    {
        $this->lectureSuperType = $lectureSuperType;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

}

<?php

namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\MuseumBundle\Entity\LessonSkill
 *
 * @ORM\Table(name="armd_lesson_skill")
 * @ORM\Entity
 */
class LessonSkill
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
     * @ORM\ManyToMany(targetEntity="\Armd\MuseumBundle\Entity\Lesson", mappedBy="skills", cascade={"persist"})
     */
    private $lessons;    

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
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
     * @return City
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
     * Constructor
     */
    public function __construct()
    {
        $this->lessons = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add lessons
     *
     * @param \Armd\MuseumBundle\Entity\Lesson $lessons
     * @return LessonSkill
     */
    public function addLesson(\Armd\MuseumBundle\Entity\Lesson $lessons)
    {
        $this->lessons[] = $lessons;
    
        return $this;
    }

    /**
     * Remove lessons
     *
     * @param \Armd\MuseumBundle\Entity\Lesson $lessons
     */
    public function removeLesson(\Armd\MuseumBundle\Entity\Lesson $lessons)
    {
        $this->lessons->removeElement($lessons);
    }

    /**
     * Get lessons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLessons()
    {
        return $this->lessons;
    }
}
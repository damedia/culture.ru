<?php

namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table(name="armd_lesson") 
 * @ORM\HasLifecycleCallbacks()
 */
class Lesson implements Taggable {
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\Column(name="published", type="boolean", nullable=true)
     */
    private $published = true;       

    /**
     * @ORM\Column(type="string", name="title")
     */
    protected $title;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\AddressBundle\Entity\City", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    private $city;
    
    /**
     * @ORM\ManyToOne(targetEntity="Armd\MuseumBundle\Entity\RealMuseum", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="museum_id", referencedColumnName="id")
     */
    private $museum;           	
    
    /**
     *
     * @ORM\Column(name="dates", type="string")
     */
    private $dates;    
    
    /**
     *
     * @ORM\Column(name="time", type="string", nullable=true)
     */
    private $time;        
    
    /**
     * @ORM\Column(name="max_members", type="integer", nullable=true)
     */
    protected $maxMembers;    
    
    /**
     * @ORM\Column(name="place", type="text", nullable=true)
     */
    protected $place;     
    
    /**
     * @ORM\ManyToOne(targetEntity="Armd\MuseumBundle\Entity\Education", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="education_id", referencedColumnName="id")
     */
    private $education;  

    /**
     * @ORM\ManyToOne(targetEntity="Armd\MuseumBundle\Entity\LessonSubject", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id")
     */
    private $subject;        
    
    /**
     * @ORM\ManyToMany(targetEntity="\Armd\MuseumBundle\Entity\LessonSkill", inversedBy="lessons")
     * @ORM\JoinTable(name="armd_lesson_lesson_skill")
     */        
    private $skills; 

    /**
     * @ORM\Column(name="age", type="string", nullable=true)
     */
    protected $age;     
           
    /**
     * @ORM\Column(name="format", type="string", nullable=true)
     */
    protected $format;       
    
    /**
     * @ORM\Column(name="url", type="string", nullable=true)
     */
    protected $url;  

    /**
     * @ORM\Column(name="announce", type="text", nullable=true)
     */
    protected $announce;
        
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;       
    
    private $tags;

    public function __toString()
    {
        return $this->getTitle();
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
        return 'armd_lesson';
    }
    
    /**
     * @return int
     */
    public function getTaggableId()
    {
        return $this->getId();
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
     * @return Lesson
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
     * Set date
     *
     * @param \DateTime $date
     * @return Lesson
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set maxMembers
     *
     * @param integer $maxMembers
     * @return Lesson
     */
    public function setMaxMembers($maxMembers)
    {
        $this->maxMembers = $maxMembers;
    
        return $this;
    }

    /**
     * Get maxMembers
     *
     * @return integer 
     */
    public function getMaxMembers()
    {
        return $this->maxMembers;
    }

    /**
     * Set place
     *
     * @param string $place
     * @return Lesson
     */
    public function setPlace($place)
    {
        $this->place = $place;
    
        return $this;
    }

    /**
     * Get place
     *
     * @return string 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set age
     *
     * @param string $age
     * @return Lesson
     */
    public function setAge($age)
    {
        $this->age = $age;
    
        return $this;
    }

    /**
     * Get age
     *
     * @return string 
     */
    public function getAge()
    {
        return $this->age;
    }

    /**
     * Set format
     *
     * @param string $format
     * @return Lesson
     */
    public function setFormat($format)
    {
        $this->format = $format;
    
        return $this;
    }

    /**
     * Get format
     *
     * @return string 
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Lesson
     */
    public function setUrl($url)
    {
        $this->url = $url;
    
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set announce
     *
     * @param string $announce
     * @return Lesson
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
     * Set description
     *
     * @param string $description
     * @return Lesson
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
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return Lesson
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
     * Set city
     *
     * @param \Armd\AddressBundle\Entity\City $city
     * @return Lesson
     */
    public function setCity(\Armd\AddressBundle\Entity\City $city = null)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get city
     *
     * @return \Armd\AddressBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set museum
     *
     * @param \Armd\MuseumBundle\Entity\RealMuseum $museum
     * @return Lesson
     */
    public function setMuseum(\Armd\MuseumBundle\Entity\RealMuseum $museum = null)
    {
        $this->museum = $museum;
    
        return $this;
    }

    /**
     * Get museum
     *
     * @return \Armd\MuseumBundle\Entity\RealMuseum 
     */
    public function getMuseum()
    {
        return $this->museum;
    }

    /**
     * Set education
     *
     * @param \Armd\MuseumBundle\Entity\Education $education
     * @return Lesson
     */
    public function setEducation(\Armd\MuseumBundle\Entity\Education $education = null)
    {
        $this->education = $education;
    
        return $this;
    }

    /**
     * Get education
     *
     * @return \Armd\MuseumBundle\Entity\Education 
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Set subject
     *
     * @param \Armd\MuseumBundle\Entity\LessonSubject $subject
     * @return Lesson
     */
    public function setSubject(\Armd\MuseumBundle\Entity\LessonSubject $subject = null)
    {
        $this->subject = $subject;
    
        return $this;
    }

    /**
     * Get subject
     *
     * @return \Armd\MuseumBundle\Entity\LessonSubject 
     */
    public function getSubject()
    {
        return $this->subject;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->skills = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add skills
     *
     * @param \Armd\MuseumBundle\Entity\LessonSkill $skills
     * @return Lesson
     */
    public function addSkill(\Armd\MuseumBundle\Entity\LessonSkill $skills)
    {
        $this->skills[] = $skills;
    
        return $this;
    }

    /**
     * Remove skills
     *
     * @param \Armd\MuseumBundle\Entity\LessonSkill $skills
     */
    public function removeSkill(\Armd\MuseumBundle\Entity\LessonSkill $skills)
    {
        $this->skills->removeElement($skills);
    }

    /**
     * Get skills
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Lesson
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
     * @return Lesson
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
     * Set dates
     *
     * @param string $dates
     * @return Lesson
     */
    public function setDates($dates)
    {
        $this->dates = $dates;
    
        return $this;
    }

    /**
     * Get dates
     *
     * @return string 
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * Set time
     *
     * @param string $time
     * @return Lesson
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return string 
     */
    public function getTime()
    {
        return $this->time;
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
}
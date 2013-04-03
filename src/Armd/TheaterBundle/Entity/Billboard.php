<?php

namespace Armd\TheaterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="armd_theater_billboard")
 */
class Billboard
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
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @ORM\ManyToOne(targetEntity="Theater")
     * @ORM\JoinColumn(name="theater_id", referencedColumnName="id")
     */
    private $theater;

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
     * @return Billboard
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
     * @return Billboard
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
     * Set theater
     *
     * @param \Armd\TheaterBundle\Entity\Theater $theater
     * @return Billboard
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
}
<?php

namespace Armd\ChronicleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\ChronicleBundle\Entity\Accident
 *
 * @ORM\Entity()
 * @ORM\Table(name="content_chronicle_accident")
 */
class Accident
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var text $announce
     *
     * @ORM\Column(type="text")
     */
    private $announce;

    /**
     * @var integer $year
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;
    
    /**
     * @ORM\ManyToOne(targetEntity="Event")
     */
    private $event;

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
     * Set announce
     *
     * @param string $announce
     * @return Accident
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
     * Set year
     *
     * @param integer $year
     * @return Accident
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
     * Set event
     *
     * @param Armd\ChronicleBundle\Entity\Event $event
     * @return Accident
     */
    public function setEvent(\Armd\ChronicleBundle\Entity\Event $event = null)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * Get event
     *
     * @return Armd\ChronicleBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }
}
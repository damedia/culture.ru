<?php

namespace Armd\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\CommonBundle\Entity as Common;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_event_shedule")
 */
class Schedule
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */    
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;
        
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
     * Set date
     *
     * @param datetime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return datetime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set event
     *
     * @param Armd\EventBundle\Entity\Event $event
     */
    public function setEvent(\Armd\EventBundle\Entity\Event $event)
    {
        $this->event = $event;
    }

    /**
     * Get event
     *
     * @return Armd\EventBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }
}
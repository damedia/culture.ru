<?php

namespace Armd\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\CommonBundle\Entity as Common;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_event_schedule")
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
    private $beginDate;
        
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;
        
    /** 
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="schedule")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")      
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

    /**
     * Set beginDate
     *
     * @param datetime $beginDate
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;
    }

    /**
     * Get beginDate
     *
     * @return datetime 
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * Set endDate
     *
     * @param datetime $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    /**
     * Get endDate
     *
     * @return datetime 
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
}
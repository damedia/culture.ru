<?php

namespace Armd\EventBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\Bundle\NewsBundle\Entity\NewsMappedSuperclass as BaseNews;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_event")
 */
class Event extends BaseNews
{
    /** 
     * @ORM\OneToOne(targetEntity="Armd\CommonBundle\Entity\Institution") 
     */
    private $place;
    
    public function __toString()
    {
        return $this->getTitle();
    }            

    /**
     * Set place
     *
     * @param Armd\CommonBundle\Entity\Institution $place
     */
    public function setPlace(\Armd\CommonBundle\Entity\Institution $place)
    {
        $this->place = $place;
    }

    /**
     * Get place
     *
     * @return Armd\CommonBundle\Entity\Institution 
     */
    public function getPlace()
    {
        return $this->place;
    }
}
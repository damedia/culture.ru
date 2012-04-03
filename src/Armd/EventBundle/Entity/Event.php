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
     * @ORM\Column(type="text")
     */
    private $place;
    
    /**
     * @ORM\Column(type="boolean", name="is_actual")
     */
    private $actual;    

    /**
     * Set place
     *
     * @param text $place
     */
    public function setPlace($place)
    {
        $this->place = $place;
    }

    /**
     * Get place
     *
     * @return text 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set actual
     *
     * @param boolean $actual
     */
    public function setActual($actual)
    {
        $this->actual = $actual;
    }

    /**
     * Get actual
     *
     * @return boolean 
     */
    public function getActual()
    {
        return $this->actual;
    }
    
    /**
     * Get actual
     *
     * @return boolean 
     */
    public function isActual()
    {
        return $this->getActual();
    }
}
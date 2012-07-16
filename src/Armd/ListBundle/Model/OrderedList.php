<?php

namespace Armd\ListBundle\Model;

abstract class OrderedList extends BaseList implements OrderedListInterface
{
    protected $position;

    /**
     * Set position
     *
     * @param integer $position
     * @return OrderedList
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }    
}

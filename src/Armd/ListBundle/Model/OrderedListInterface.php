<?php

namespace Armd\ListBundle\Model;

interface OrderedListInterface
{
    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle();

    /**
     * Set position
     *
     * @param integer $position
     */
    public function setPosition($position);

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition();
}

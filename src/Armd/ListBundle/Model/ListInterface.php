<?php

namespace Armd\ListBundle\Model;

interface ListInterface
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
}

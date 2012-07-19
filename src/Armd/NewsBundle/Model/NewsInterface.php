<?php

namespace Armd\NewsBundle\Model;

interface NewsInterface
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
     * Set date
     *
     * @param datetime $date
     */
    public function setDate($date);

    /**
     * Get date
     *
     * @return datetime
     */
    public function getDate();
}

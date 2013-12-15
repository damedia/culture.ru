<?php

namespace Armd\NewsBundle\Model;

interface NewsInterface {
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
     * @param \DateTime  $date
     */
    public function setDate(\DateTime $date);

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate();
}

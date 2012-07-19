<?php

namespace Armd\NewsBundle\Model;

use Armd\ListBundle\Model\BaseList;

class News extends BaseList implements NewsInterface
{    
    protected $date;

    /**
     * Set title
     *
     * @param string $title
     * @return BaseList
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set date
     *
     * @param datetime $date
     * @return News
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
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
}
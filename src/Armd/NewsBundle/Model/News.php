<?php

namespace Armd\NewsBundle\Model;

use Armd\ListBundle\Model\BaseList;

class News extends BaseList implements NewsInterface
{    
    protected $date;

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return News
     */
    public function setDate(\DateTime $date)
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
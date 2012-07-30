<?php

namespace Armd\ListBundle\Model;

abstract class BaseList implements ListInterface
{
    protected $id;
        
    protected $title;

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
     * Get title
     *
     * @return string 
     */    
    public function __toString()
    {
        return (string) $this->getTitle();
    }    
}

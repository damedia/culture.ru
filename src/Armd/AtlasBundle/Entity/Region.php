<?php

namespace Armd\AtlasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\AtlasBundle\Entity\Region
 *
 * @ORM\Table(name="atlas_region")
 * @ORM\Entity
 */
class Region
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(name="sort_index", type="integer", nullable=false)
     */
    private $sortIndex = 500;


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }


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
     * @return Region
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

    public function getSortIndex()
    {
        return $this->sortIndex;
    }

    public function setSortIndex($sortIndex)
    {
        $this->sortIndex = $sortIndex;
    }
}
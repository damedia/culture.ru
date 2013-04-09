<?php

namespace Armd\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\AddressBundle\Entity\City
 *
 * @ORM\Table(name="address_city")
 * @ORM\Entity
 */
class City
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
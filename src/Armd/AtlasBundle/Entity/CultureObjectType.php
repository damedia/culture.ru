<?php

namespace Armd\AtlasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\AtlasBundle\Entity\Subject;

/**
 * Armd\AtlasBundle\Entity\CultureObjectType
 *
 * @ORM\Table(name="content_culture_object_type")
 * @ORM\Entity
 */
class CultureObjectType
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
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @ORM\OneToMany(targetEntity="CultureObject", mappedBy="type")
     */
    protected $objects;

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
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }



    /**
     * Set icon
     *
     * @param string $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * Get icon
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }
    public function __construct()
    {
        $this->objects = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add objects
     *
     * @param Armd\AtlasBundle\Entity\CultureObject $objects
     * @return CultureObjectType
     */
    public function addObject(\Armd\AtlasBundle\Entity\CultureObject $objects)
    {
        $this->objects[] = $objects;
        return $this;
    }

    /**
     * Remove objects
     *
     * @param <variableType$objects
     */
    public function removeObject(\Armd\AtlasBundle\Entity\CultureObject $objects)
    {
        $this->objects->removeElement($objects);
    }

    /**
     * Get objects
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getObjects()
    {
        return $this->objects;
    }
}
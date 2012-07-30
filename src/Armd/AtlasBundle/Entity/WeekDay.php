<?php

namespace Armd\AtlasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\AtlasBundle\Entity\Category
 *
 * @ORM\Table(name="atlas_weekday")
 * @ORM\Entity
 */
class WeekDay
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
    * @ORM\Column(name="sort_index", type="integer", nullable=false)
    */
    private $sortIndex = 500;

    public function __toString()
    {
        return $this->getName();
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
     * Set name
     *
     * @param string $name
     * @return WeekDay
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set sortIndex
     *
     * @param integer $sortIndex
     * @return WeekDay
     */
    public function setSortIndex($sortIndex)
    {
        $this->sortIndex = $sortIndex;
        return $this;
    }

    /**
     * Get sortIndex
     *
     * @return integer 
     */
    public function getSortIndex()
    {
        return $this->sortIndex;
    }
}
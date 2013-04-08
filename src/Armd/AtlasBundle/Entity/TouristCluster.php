<?php

namespace Armd\AtlasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\AtlasBundle\Entity\TouristCluster
 *
 * @ORM\Table(name="atlas_tourist_cluster")
 * @ORM\Entity(repositoryClass="Armd\AtlasBundle\Repository\TouristClusterRepository")
 */
class TouristCluster
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
    private $sortIndex = 0;


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
     * @param string $title
     * @return Armd\AtlasBundle\Entity\TouristCluster
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
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
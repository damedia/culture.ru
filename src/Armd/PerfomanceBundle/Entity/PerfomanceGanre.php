<?php

namespace Armd\PerfomanceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="content_perfomance_ganre")
 * @ORM\Entity
 */
class PerfomanceGanre
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="\Armd\PerfomanceBundle\Entity\Perfomance", mappedBy="ganres", cascade={"persist"})
     */
    private $perfomances;

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
     * @return PerfomanceGanre
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
    
    public function __toString()
    {
        return $this->getTitle();
    }    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->perfomances = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add perfomances
     *
     * @param \Armd\PerfomanceBundle\Entity\Perfomance $perfomances
     * @return PerfomanceGanre
     */
    public function addPerfomance(\Armd\PerfomanceBundle\Entity\Perfomance $perfomances)
    {
        $this->perfomances[] = $perfomances;
    
        return $this;
    }

    /**
     * Remove perfomances
     *
     * @param \Armd\PerfomanceBundle\Entity\Perfomance $perfomances
     */
    public function removePerfomance(\Armd\PerfomanceBundle\Entity\Perfomance $perfomances)
    {
        $this->perfomances->removeElement($perfomances);
    }

    /**
     * Get perfomances
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPerfomances()
    {
        return $this->perfomances;
    }
}
<?php

namespace Armd\TaxonomyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="taxonomy_tag")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */    
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;    
    
    /** 
     * @ORM\OneToMany(targetEntity="TagContentReference", mappedBy="tag") 
     */
    private $entities;
    
    public function __construct()
    {
        $this->entities = new \Doctrine\Common\Collections\ArrayCollection();
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
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Add entities
     *
     * @param Armd\TaxonomyBundle\Entity\TagContentReference $entities
     */
    public function addTagContentReference(\Armd\TaxonomyBundle\Entity\TagContentReference $entities)
    {
        $this->entities[] = $entities;
    }

    /**
     * Get entities
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getEntities()
    {
        return $this->entities;
    }
}
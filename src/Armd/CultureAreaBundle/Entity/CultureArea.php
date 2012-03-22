<?php

namespace Armd\CultureAreaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/** 
 * @ORM\Entity 
 * @ORM\Table(name="content_culture_area")
 * @Gedmo\Tree(type="nested")
 */
class CultureArea
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
    private $title;
    
    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="CultureArea", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="CultureArea", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;    

    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Get title
     *
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
     * Set lft
     *
     * @param integer $lft
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    }

    /**
     * Get lft
     *
     * @return integer 
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set lvl
     *
     * @param integer $lvl
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
    }

    /**
     * Get lvl
     *
     * @return integer 
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    /**
     * Set rgt
     *
     * @param integer $rgt
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    }

    /**
     * Get rgt
     *
     * @return integer 
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set root
     *
     * @param integer $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * Get root
     *
     * @return integer 
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set parent
     *
     * @param Armd\CultureAreaBundle\Entity\CultureArea $parent
     */
    public function setParent(\Armd\CultureAreaBundle\Entity\CultureArea $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return Armd\CultureAreaBundle\Entity\CultureArea 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param Armd\CultureAreaBundle\Entity\CultureArea $children
     */
    public function addCultureArea(\Armd\CultureAreaBundle\Entity\CultureArea $children)
    {
        $this->children[] = $children;
    }

    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }
}
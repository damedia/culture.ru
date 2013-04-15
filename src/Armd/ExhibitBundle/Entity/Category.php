<?php

namespace Armd\ExhibitBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tree\Node;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Armd\ExhibitBundle\Entity\Category
 *
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="exhibit_category")
 * @ORM\Entity(repositoryClass="Armd\ExhibitBundle\Repository\CategoryRepository")
 */
class Category implements Node
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
    
    /**
     * @ORM\Column(type="boolean", nullable=false)
     */        
    protected $published = false;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;

//    /**
//     * @ORM\ManyToMany(targetEntity="Object", mappedBy="categories", cascade={"persist"})
//     */
//    private $objects;


    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->objects = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getDotLeveledTitle();
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
     * @return Category
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

    public function getSpaceLeveledTitle()
    {
        return $this->getLeveledTitle("&nbsp;&nbsp;&nbsp;&nbsp;");
    }

    public function getDotLeveledTitle()
    {
        return $this->getLeveledTitle('....');
    }

    public function getLeveledTitle($padWith)
    {
        $prefix = "";
        for ($i = 2; $i <= $this->lvl; $i++) {
            $prefix .= $padWith;
        }
        return $prefix . $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Category
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set published
     *
     * @param boolean $published
     * @return ArtObject
     */
    public function setPublished($published)
    {
        $this->published = $published;
    
        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set root
     *
     * @param integer $root
     * @return Category
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
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
     * Set lvl
     *
     * @param integer $lvl
     * @return Category
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;

        return $this;
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
     * Set lft
     *
     * @param integer $lft
     * @return Category
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
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
     * Set rgt
     *
     * @param integer $rgt
     * @return Category
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
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
     * Set parent
     *
     * @param Armd\ExhibitBundle\Entity\Category $parent
     * @return Category
     */
    public function setParent(\Armd\ExhibitBundle\Entity\Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Armd\ExhibitBundle\Entity\Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param Armd\ExhibitBundle\Entity\Category $children
     * @return Category
     */
    public function addChildren(\Armd\ExhibitBundle\Entity\Category $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param Armd\ExhibitBundle\Entity\Category $children
     */
    public function removeChildren(\Armd\ExhibitBundle\Entity\Category $children)
    {
        $this->children->removeElement($children);
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

    /**
     * @return string|null
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

}
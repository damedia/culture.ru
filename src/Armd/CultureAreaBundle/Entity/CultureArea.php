<?php

namespace Armd\CultureAreaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Armd\ContentAbstractBundle\Entity\BaseContent;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;

/** 
 * @ORM\Entity(repositoryClass="Armd\CultureAreaBundle\Entity\CultureAreaRepository")
 * @ORM\Table(name="content_culture_area")
 * @Gedmo\Tree(type="nested")
 */
class CultureArea extends BaseContent implements TaxonomyInterface
{
    /**
     * @ORM\Column(type="string")
     */    
    private $title;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $announce;    
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;    
    
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
    
    /**
     * @ORM\Column(type="string", nullable=true, name="_tag")
     */    
    private $personalTag;

    /**
     * @ORM\Column(type="text", nullable=true, name="_tags")
     */    
    private $tags;    
    
    /**
     * @ORM\ManyToOne(targetEntity="\Armd\Bundle\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="\Armd\Bundle\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    private $gallery;        

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

    /**
     * Set announce
     *
     * @param text $announce
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
    }

    /**
     * Get announce
     *
     * @return text 
     */
    public function getAnnounce()
    {
        return $this->announce;
    }

    /**
     * Set body
     *
     * @param text $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get body
     *
     * @return text 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set image
     *
     * @param Armd\Bundle\MediaBundle\Entity\Media $image
     */
    public function setImage(\Armd\Bundle\MediaBundle\Entity\Media $image)
    {
        $this->image = $image;
    }

    /**
     * Get image
     *
     * @return Armd\Bundle\MediaBundle\Entity\Media 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set gallery
     *
     * @param Armd\Bundle\MediaBundle\Entity\Gallery $gallery
     */
    public function setGallery(\Armd\Bundle\MediaBundle\Entity\Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * Get gallery
     *
     * @return Armd\Bundle\MediaBundle\Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set personalTag
     *
     * @param string $personalTag
     */
    public function setPersonalTag($personalTag)
    {
        $this->personalTag = $personalTag;
    }

    /**
     * Get personalTag
     *
     * @return string 
     */
    public function getPersonalTag()
    {
        return $this->personalTag;
    }

    /**
     * Set tags
     *
     * @param text $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Get tags
     *
     * @return text 
     */
    public function getTags()
    {
        return $this->tags;
    }
}
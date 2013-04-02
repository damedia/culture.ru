<?php

namespace Armd\LectureBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tree\Node;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Table(name="lecture_category")
 * @ORM\Entity(repositoryClass="Gedmo\Tree\Entity\Repository\NestedTreeRepository")
 */
class LectureCategory implements Node
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
     * @ORM\ManyToOne(targetEntity="LectureCategory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="LectureCategory", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="LectureSuperType")
     * @ORM\JoinColumn(name="super_type_id")
    */
    private $lectureSuperType;

    /**
     * @ORM\ManyToMany(targetEntity="Lecture", mappedBy="categories", cascade={"persist"})
     */
    private $lectures;

    /**
     * @ORM\Column(name="system_slug", type="string", nullable=true)
     */
    private $systemSlug;


    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->lectures = new ArrayCollection();
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
     * @return LectureCategory
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
     * Set root
     *
     * @param integer $root
     * @return LectureCategory
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
     * @return LectureCategory
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
     * @return LectureCategory
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
     * @return LectureCategory
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
     * @param \Armd\LectureBundle\Entity\LectureCategory $parent
     * @return LectureCategory
     */
    public function setParent(\Armd\LectureBundle\Entity\LectureCategory $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \Armd\LectureBundle\Entity\LectureCategory
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \Armd\LectureBundle\Entity\LectureCategory $children
     * @return LectureCategory
     */
    public function addChildren(\Armd\LectureBundle\Entity\LectureCategory $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Armd\LectureBundle\Entity\LectureCategory $children
     */
    public function removeChildren(\Armd\LectureBundle\Entity\LectureCategory $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add lecture
     *
     * @param \Armd\LectureBundle\Entity\Lecture $lecture
     * @return \Armd\LectureBundle\Entity\LectureCategory
     */
    public function addLecture(\Armd\LectureBundle\Entity\Lecture $lecture)
    {
        $this->lectures[] = $lecture;

        return $this;
    }

    /**
     * @return LectureSuperType
     */
    public function getLectureSuperType()
    {
        return $this->lectureSuperType;
    }

    /**
     * @param LectureSuperType $superType
     * @return LectureCategory
     */
    public function setLectureSuperType(LectureSuperType $superType)
    {
        $this->lectureSuperType = $superType;
        return $this;
    }


    /**
     * Remove lecture
     *
     * @param \Armd\LectureBundle\Entity\Lecture $lecture
     */
    public function removeLecture(\Armd\LectureBundle\Entity\Lecture $lecture)
    {
        $this->lectures->removeElement($lecture);
    }

    /**
     * Get objects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLectures()
    {
        return $this->lectures;
    }

    public function getSystemSlug()
    {
        return $this->systemSlug;
    }

    public function setSystemSlug($systemSlug)
    {
        $this->systemSlug = $systemSlug;
    }

}
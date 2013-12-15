<?php

namespace Armd\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="content_news_category")
 * @ORM\Entity(repositoryClass="Armd\NewsBundle\Repository\CategoryRepository")
 */
class Category {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $slug;    
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $priority;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $filtrable;
    
    protected $selected;    
    
    public function __toString()
    {
        return $this->getTitle();
    }
    
    public function isSelected()
    {
        return $this->selected;
    }
    
    public function setSelected($selected)
    {
        $this->selected = $selected;
        
        return $this;
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

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Category
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set filtrable
     *
     * @param boolean $filtrable
     * @return Category
     */
    public function setFiltrable($filtrable)
    {
        $this->filtrable = $filtrable;
        return $this;
    }

    /**
     * Get filtrable
     *
     * @return boolean 
     */
    public function getFiltrable()
    {
        return $this->filtrable;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
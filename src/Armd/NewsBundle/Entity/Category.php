<?php

namespace Armd\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="content_news_category") 
 */
class Category
{
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
     * @ORM\Column(type="integer")
     */
    protected $priority;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $filtrable;    
    
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
}
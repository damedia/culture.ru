<?php

namespace Armd\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\NewsBundle\Model\News as BaseNews;

/**
 * @ORM\Entity(repositoryClass="Armd\NewsBundle\Repository\NewsRepository")
 * @ORM\Table(name="content_news") 
 */
class News extends BaseNews
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
     * @ORM\Column(type="datetime", name="news_date")
     */
    protected $date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */    
    protected $announce;

    /**
     * @ORM\Column(type="text")
     */    
    protected $body;
    
    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\OrderBy({"title" = "ASC"})     
     **/
    protected $category;

    /**
     * @ORM\Column(type="boolean")
     */        
    protected $important;

    /**
     * @ORM\Column(type="boolean")
     */        
    protected $published;

    /**
     * Set announce
     *
     * @param text $announce
     * @return News
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
        return $this;
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
     * @return News
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
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
     * Set category
     *
     * @param Armd\NewsBundle\Entity\Category $category
     * @return News
     */
    public function setCategory(\Armd\NewsBundle\Entity\Category $category = null)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * Get category
     *
     * @return Armd\NewsBundle\Entity\Categoty 
     */
    public function getCategory()
    {
        return $this->category;
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
     * @return News
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
     * Set important
     *
     * @param boolean $important
     * @return News
     */
    public function setImportant($important)
    {
        $this->important = $important;
        return $this;
    }

    /**
     * Get important
     *
     * @return boolean 
     */
    public function getImportant()
    {
        return $this->important;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return News
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
}
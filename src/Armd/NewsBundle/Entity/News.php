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
     * @ORM\Column(type="datetime")
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
}
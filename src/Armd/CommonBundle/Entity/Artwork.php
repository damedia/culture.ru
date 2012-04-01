<?php

namespace Armd\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\Bundle\CmsBundle\Entity\BaseContent;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_artwork")
 */
class Artwork extends BaseContent
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
     * @ORM\Column(type="text")
     */
    private $body;
    
    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="artworks")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     */    
    private $author;

    /**
     * @ORM\Column(type="string", nullable=true)
     */    
    private $place;
    
    /**
     * @ORM\Column(type="date", name="artwork_date", nullable=true)
     */    
    private $date;

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
     * Set place
     *
     * @param string $place
     */
    public function setPlace($place)
    {
        $this->place = $place;
    }

    /**
     * Get place
     *
     * @return string 
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set date
     *
     * @param date $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get date
     *
     * @return date 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set author
     *
     * @param Armd\CommonBundle\Entity\Person $author
     */
    public function setAuthor(\Armd\CommonBundle\Entity\Person $author)
    {
        $this->author = $author;
    }

    /**
     * Get author
     *
     * @return Armd\CommonBundle\Entity\Person 
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
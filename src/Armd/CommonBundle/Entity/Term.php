<?php

namespace Armd\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\ContentAbstractBundle\Entity\BaseContent;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_term")
 */
class Term extends BaseContent implements TaxonomyInterface
{
    /**
     * @ORM\Column(type="string")
     */
    private $title;
        
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $href;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $announce;        
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;
    
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

    /**
     * Set title
     *
     * @param string $title
     * @return Term
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
     * Set href
     *
     * @param string $href
     * @return Term
     */
    public function setHref($href)
    {
        $this->href = $href;
        return $this;
    }

    /**
     * Get href
     *
     * @return string 
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Set announce
     *
     * @param text $announce
     * @return Term
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
     * @return Term
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
     * Set personalTag
     *
     * @param string $personalTag
     * @return Term
     */
    public function setPersonalTag($personalTag)
    {
        $this->personalTag = $personalTag;
        return $this;
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
     * @return Term
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
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

    /**
     * Set image
     *
     * @param Armd\Bundle\MediaBundle\Entity\Media $image
     * @return Term
     */
    public function setImage(\Armd\Bundle\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;
        return $this;
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
     * @return Term
     */
    public function setGallery(\Armd\Bundle\MediaBundle\Entity\Gallery $gallery = null)
    {
        $this->gallery = $gallery;
        return $this;
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
}
<?php

namespace Armd\ExtendedNewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Armd\NewsBundle\Entity\BaseNews;
use Armd\TaxonomyBundle\Model\TaxonomyInterface;

/**
 * @ORM\Entity(repositoryClass="Armd\ExtendedNewsBundle\Repository\NewsRepository")
 * @ORM\Table(name="content_news_extended")
 */
class News extends BaseNews
//implements TaxonomyInterface
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $source;
    
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
     * Set source
     *
     * @param string $source
     * @return News
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set personalTag
     *
     * @param string $personalTag
     * @return News
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
     * @return News
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
     * @return News
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
     * @return News
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
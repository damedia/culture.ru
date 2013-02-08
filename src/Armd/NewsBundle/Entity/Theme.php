<?php

namespace Armd\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="content_news_theme")
 */
class Theme
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
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="icon_media_id", nullable=true)
     */
    protected $iconMedia;

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
     * @return Theme
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
     * Set iconMedia
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $iconMedia
     * @return Theme
     */
    public function setIconMedia(\Application\Sonata\MediaBundle\Entity\Media $iconMedia = null)
    {
        $this->iconMedia = $iconMedia;
    
        return $this;
    }

    /**
     * Get iconMedia
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getIconMedia()
    {
        return $this->iconMedia;
    }
}
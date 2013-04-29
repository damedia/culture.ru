<?php

namespace Armd\TouristRouteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Armd\TouristRouteBundle\Entity\Category
 *
 * @ORM\Table(name="tourist_route_category")
 * @ORM\Entity
 */
class Category
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
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="icon_id", nullable=true)
     */
    private $icon;

    /**
     * @ORM\Column(name="slug", type="string", length=255, nullable=true)
     */
    private $slug;


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
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
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
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param \Application\Sonata\MediaBundle\Entity\Media $icon
     * @return Category
     */
    public function setIcon(\Application\Sonata\MediaBundle\Entity\Media $icon = null)
    {
        if(is_null($icon) || $icon->isUploaded()) {
            $this->icon = $icon;
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }
}
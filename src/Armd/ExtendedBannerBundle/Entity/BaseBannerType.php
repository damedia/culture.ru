<?php

namespace Armd\ExtendedBannerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="banner_type")
 */

class BaseBannerType
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="Armd\ExtendedBannerBundle\Entity\BaseBanner", mappedBy="type")
     */
    private $banners;

    public function __construct()
    {
        $this->banners = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
     * Set name
     *
     * @param string $name
     * @return BannerType
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return BannerType
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    public function getBannerCount()
    {
        return count($this->banners);
    }

    public function getBanners()
    {
        return $this->banners;
    }

    public function setBanners($banners)
    {
        foreach($banners as $banner) {
            $banner->setType($this);
        }
        $this->banners = $banners;
    }

    /**
     * Add banners
     *
     * @param Armd\ExtendedBannerBundle\Entity\Banner $banners
     * @return BannerType
     */
    public function addBanner(\Armd\ExtendedBannerBundle\Entity\BaseBanner $banners)
    {
        $this->banners[] = $banners;
        return $this;
    }

    /**
     * Remove banners
     *
     * @param <variableType$banners
     */
    public function removeBanner(\Armd\ExtendedBannerBundle\Entity\BaseBanner $banners)
    {
        $this->banners->removeElement($banners);
    }
}
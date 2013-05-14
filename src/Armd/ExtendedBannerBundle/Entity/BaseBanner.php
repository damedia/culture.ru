<?php

namespace Armd\ExtendedBannerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="Armd\ExtendedBannerBundle\Repository\BannerRepository")
 * @ORM\Table(name="banner")
 */
class BaseBanner
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
     * @ORM\Column(name="href", type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $href = '#';

    /**
     * @ORM\Column(name="start_date", type="date", nullable=false)
     */
    private $startDate;

    /**
     * @ORM\Column(name="end_date", type="date", nullable=false)
     */
    private $endDate;

    /**
     * @ORM\Column(name="image_path", type="string", length=255, nullable=true)
     */
    private $imagePath;

    private $oldImagePath;

    /**
     * @Assert\Image(maxSize="6000000")
     */
    private $imageFile;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\Column(name="priority", type="integer", nullable=false)
     */
    private $priority = 0;

    /**
     * @ORM\Column(name="max_views", type="integer", nullable=false)
     */
    private $maxViews = 0;

    /**
     * @ORM\Column(name="view_count", type="integer", nullable=false)
     */
    private $viewCount = 0;

    /**
     * @ORM\Column(name="click_count", type="integer", nullable=false)
     */
    private $clickCount = 0;

    /**
     * @ORM\Column(name="open_in_new_window", type="boolean", nullable=true)
     */
    private $openInNewWindow = true;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\ExtendedBannerBundle\Entity\BaseBannerType", inversedBy="banners")
     * @ORM\JoinColumn(name="banner_type_id", referencedColumnName="id", nullable=false)
     */
    private $type;

    /**
     * @ORM\Column(name="updated", type="integer", nullable=false)
     */
    private $updated = 0;

    public function __construct()
    {
        $this->startDate = new \DateTime();
        $this->endDate = new \DateTime();
    }

    public function addClick()
    {
        $this->clickCount++;

    }

    public function addView()
    {
        $this->viewCount++;
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
     * @return Banner
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
     * Get href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Set href
     *
     * @param string $href
     * @return string
     */
    public function setHref($href)
    {
        $this->href = $href;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return Banner
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return BaseBanner
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Get imagePath
     *
     * @return string
     */
    public function getImagePath()
    {
        return $this->imagePath;
    }
    
    /**
     * Set image
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $image
     * @return Application\Sonata\MediaBundle\Entity\Media
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getImage()
    {
        return $this->image;
    }

    /*public function getAbsolutePath()
    {
        return null === $this->imagePath ? null : $this->getUploadRootDir() . '/' . $this->imagePath;
    }

    public function getWebPath()
    {
        return null === $this->imagePath ? null : '/' . $this->getUploadDir() . '/' . $this->imagePath;
    }

    public static function getUploadRootDir()
    {
        // the absolute directory path where uploaded documents should be saved
        return realpath(__DIR__ . '/../../../../../../web/') . '/' . static::getUploadDir();
    }

    protected static function getUploadDir()
    {
        // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
        return 'uploads/banners';
    }*/

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Banner
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
     * Set maxViews
     *
     * @param integer $maxViews
     * @return Banner
     */
    public function setMaxViews($maxViews)
    {
        $this->maxViews = $maxViews;
        return $this;
    }

    /**
     * Get maxViews
     *
     * @return integer
     */
    public function getMaxViews()
    {
        return $this->maxViews;
    }

    /**
     * Set viewCount
     *
     * @param integer $viewCount
     * @return Banner
     */
    public function setViewCount($viewCount)
    {
        $this->viewCount = $viewCount;
        return $this;
    }

    /**
     * Get viewCount
     *
     * @return integer
     */
    public function getViewCount()
    {
        return $this->viewCount;
    }

    /**
     * Set clickCount
     *
     * @param integer $clickCount
     * @return Banner
     */
    public function setClickCount($clickCount)
    {
        $this->clickCount = $clickCount;
        return $this;
    }

    /**
     * Get clickCount
     *
     * @return integer
     */
    public function getClickCount()
    {
        return $this->clickCount;
    }

    /**
     * @return bool
     */
    public function getOpenInNewWindow()
    {
        return $this->openInNewWindow;
    }

    /**
     * @param boolean $openInNewWindow
     * @return BaseBanner
     */
    public function setOpenInNewWindow($openInNewWindow)
    {
        $this->openInNewWindow = $openInNewWindow;
        return $this;
    }

    /**
     * Set type
     *
     * @param \Armd\ExtendedBannerBundle\Entity\BaseBannerType $type
     * @return Banner
     */
    public function setType(\Armd\ExtendedBannerBundle\Entity\BaseBannerType $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return \Armd\ExtendedBannerBundle\Entity\BaseBannerType
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Set updated
     *
     * @param integer $updated
     * @return Banner
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Get updated
     *
     * @return integer
     */
    public function getUpdated()
    {
        return $this->updated;
    }


}
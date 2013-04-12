<?php

namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Sonata\MediaBundle\Entity\Media;

/**
 * @ORM\Entity()
 * @ORM\Table(name="armd_museum_guide") 
 */
class MuseumGuide
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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $announce;
    
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $body;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"})
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;


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
     * @return MuseumGuide
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
     * Set announce
     *
     * @param string $announce
     * @return MuseumGuide
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getAnnounce()
    {
        return $this->announce;
    }

    /**
     * Set body
     *
     * @param string $body
     * @return MuseumGuide
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * Set city
     *
     * @param string $city
     * @return MuseumGuide
     */
    public function setCity($city)
    {
        $this->city = $city;
    
        return $this;
    }

    /**
     * Get region
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set image
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $image
     * @return Museum
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

    /**
     * Set file
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $file
     * @return PaperArchive
     */
    public function setFile(\Application\Sonata\MediaBundle\Entity\Media $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return Application\Sonata\MediaBundle\Entity\Media
     */
    public function getFile()
    {
        return $this->file;
    }
}
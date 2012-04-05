<?php

namespace Armd\CultureMapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Application\Sonata\MediaBundle\Entity\Media;
use Application\Sonata\MediaBundle\Entity\Gallery;

/**
 * Armd\CultureMapBundle\Entity\Subject
 *
 * @ORM\Table(name="content_subject")
 * @ORM\Entity
 */
class Subject
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */    
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     */    
    private $title;
    
    /**
     * @ORM\Column(name="yname", type="string", length=255, nullable="true")
     */
    private $yname;

    /**
     * @ORM\Column(name="announce", type="text", nullable="true")
     */    
    private $announce;
    
    /**
     * @ORM\Column(name="text", type="text", nullable="true")
     */    
    private $text;

    /**
     * @ORM\Column(name="square", type="string", length=255, nullable="true")
     */
    private $square;

    /**
     * @ORM\Column(name="city_count", type="string", length=255, nullable="true")
     */
    private $cityCount;

    /**
     * @ORM\Column(name="village_count", type="string", length=255, nullable="true")
     */
    private $villageCount;

    /**
     * @ORM\Column(name="city_population", type="string", length=255, nullable="true")
     */
    private $cityPopulation;

    /**
     * @ORM\Column(name="village_population", type="string", length=255, nullable="true")
     */
    private $villagePopulation;

    /**
     * @ORM\Column(name="administrative_center", type="string", length=255, nullable="true")
     */
    private $administrativeCenter;

    /**
     * @ORM\Column(name="nationality", type="string", length=255, nullable="true")
     */
    private $nationality;

    /**
     * @ORM\Column(name="language", type="string", length=255, nullable="true")
     */
    private $language;

    /**
     * @ORM\Column(name="religion", type="string", length=255, nullable="true")
     */
    private $religion;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="logo_id", referencedColumnName="id")
     */
    private $logo;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Gallery")
     * @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
     */
    private $gallery;

    /**
     * @return string
     */
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
     * Set announce
     *
     * @param text $announce
     */
    public function setAnnounce($announce)
    {
        $this->announce = $announce;
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
     * Set text
     *
     * @param text $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * Get text
     *
     * @return text 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set square
     *
     * @param string $square
     */
    public function setSquare($square)
    {
        $this->square = $square;
    }

    /**
     * Get square
     *
     * @return string 
     */
    public function getSquare()
    {
        return $this->square;
    }

    /**
     * Set cityCount
     *
     * @param string $cityCount
     */
    public function setCityCount($cityCount)
    {
        $this->cityCount = $cityCount;
    }

    /**
     * Get cityCount
     *
     * @return string 
     */
    public function getCityCount()
    {
        return $this->cityCount;
    }

    /**
     * Set villageCount
     *
     * @param string $villageCount
     */
    public function setVillageCount($villageCount)
    {
        $this->villageCount = $villageCount;
    }

    /**
     * Get villageCount
     *
     * @return string 
     */
    public function getVillageCount()
    {
        return $this->villageCount;
    }

    /**
     * Set cityPopulation
     *
     * @param string $cityPopulation
     */
    public function setCityPopulation($cityPopulation)
    {
        $this->cityPopulation = $cityPopulation;
    }

    /**
     * Get cityPopulation
     *
     * @return string 
     */
    public function getCityPopulation()
    {
        return $this->cityPopulation;
    }

    /**
     * Set villagePopulation
     *
     * @param string $villagePopulation
     */
    public function setVillagePopulation($villagePopulation)
    {
        $this->villagePopulation = $villagePopulation;
    }

    /**
     * Get villagePopulation
     *
     * @return string 
     */
    public function getVillagePopulation()
    {
        return $this->villagePopulation;
    }

    /**
     * Set administrativeCenter
     *
     * @param string $administrativeCenter
     */
    public function setAdministrativeCenter($administrativeCenter)
    {
        $this->administrativeCenter = $administrativeCenter;
    }

    /**
     * Get administrativeCenter
     *
     * @return string 
     */
    public function getAdministrativeCenter()
    {
        return $this->administrativeCenter;
    }

    /**
     * Set nationality
     *
     * @param string $nationality
     */
    public function setNationality($nationality)
    {
        $this->nationality = $nationality;
    }

    /**
     * Get nationality
     *
     * @return string 
     */
    public function getNationality()
    {
        return $this->nationality;
    }

    /**
     * Set language
     *
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get language
     *
     * @return string 
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set religion
     *
     * @param string $religion
     */
    public function setReligion($religion)
    {
        $this->religion = $religion;
    }

    /**
     * Get religion
     *
     * @return string 
     */
    public function getReligion()
    {
        return $this->religion;
    }

    /**
     * Set logo
     *
     * @param Application\Sonata\MediaBundle\Entity\Media $logo
     */
    public function setLogo(\Application\Sonata\MediaBundle\Entity\Media $logo)
    {
        $this->logo = $logo;
    }

    /**
     * Get logo
     *
     * @return Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set gallery
     *
     * @param Application\Sonata\MediaBundle\Entity\Gallery $gallery
     */
    public function setGallery(\Application\Sonata\MediaBundle\Entity\Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    /**
     * Get gallery
     *
     * @return Application\Sonata\MediaBundle\Entity\Gallery 
     */
    public function getGallery()
    {
        return $this->gallery;
    }

    /**
     * Set yname
     *
     * @param string $yname
     */
    public function setYname($yname)
    {
        $this->yname = $yname;
    }

    /**
     * Get yname
     *
     * @return string 
     */
    public function getYname()
    {
        return $this->yname;
    }
}
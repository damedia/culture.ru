<?php

namespace Armd\CultureMapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\CultureMapBundle\Entity\Subject;
use Armd\CultureMapBundle\Entity\CultureObjectType;

/**
 * Armd\CultureMapBundle\Entity\CultureObject
 *
 * @ORM\Table(name="content_culture_object")
 * @ORM\Entity
 */
class CultureObject
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
     * @ORM\Column(name="announce", type="text", nullable="true")
     */
    private $announce;

    /**
     * @ORM\Column(name="text", type="text", nullable="true")
     */
    private $text;

    /**
     * @ORM\Column(name="site", type="string", nullable="true")
     */
    private $site;

    /**
     * @ORM\Column(name="email", type="string", nullable="true")
     */
    private $email;

    /**
     * @ORM\Column(name="phone", type="string", nullable="true")
     */
    private $phone;

    /**
     * @ORM\Column(name="latitude", type="string", length=12, nullable="true")
     */
    private $latitude;

    /**
     * @ORM\Column(name="longitude", type="string", length=12, nullable="true")
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity="Subject")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id", nullable="true")
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity="CultureObjectType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    private $type;

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
     * Set subject
     *
     * @param Armd\CultureMapBundle\Entity\Subject $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Get subject
     *
     * @return Armd\CultureMapBundle\Entity\Subject 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set type
     *
     * @param Armd\CultureMapBundle\Entity\CultureObjectType $type
     */
    public function setType(\Armd\CultureMapBundle\Entity\CultureObjectType $type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return Armd\CultureMapBundle\Entity\CultureObjectType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set site
     *
     * @param text $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * Get site
     *
     * @return text 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set email
     *
     * @param text $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return text 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param text $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Get phone
     *
     * @return text 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}
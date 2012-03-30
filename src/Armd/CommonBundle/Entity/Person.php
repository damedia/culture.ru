<?php

namespace Armd\CommonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\Bundle\CmsBundle\Entity\BaseContent;

/**
 * @ORM\Entity
 * @ORM\Table(name="content_person")
 */
class Person extends BaseContent
{
    /**
     * @ORM\Column(type="string")
     */
    private $lastname;
    
    /**
     * @ORM\Column(type="string")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $middlename;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;
    
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $deathday;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $birthpalce;
    
    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        $title = "{$this->getLastname()} {$this->getFirstname()} {$this->getMiddlename()}";
        
        return preg_replace('/\s+/', ' ', $title) ;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set middlename
     *
     * @param string $middlename
     */
    public function setMiddlename($middlename)
    {
        $this->middlename = $middlename;
    }

    /**
     * Get middlename
     *
     * @return string 
     */
    public function getMiddlename()
    {
        return $this->middlename;
    }

    /**
     * Set birthday
     *
     * @param date $birthday
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }

    /**
     * Get birthday
     *
     * @return date 
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set deathday
     *
     * @param date $deathday
     */
    public function setDeathday($deathday)
    {
        $this->deathday = $deathday;
    }

    /**
     * Get deathday
     *
     * @return date 
     */
    public function getDeathday()
    {
        return $this->deathday;
    }

    /**
     * Set birthpalce
     *
     * @param string $birthpalce
     */
    public function setBirthpalce($birthpalce)
    {
        $this->birthpalce = $birthpalce;
    }

    /**
     * Get birthpalce
     *
     * @return string 
     */
    public function getBirthpalce()
    {
        return $this->birthpalce;
    }
}
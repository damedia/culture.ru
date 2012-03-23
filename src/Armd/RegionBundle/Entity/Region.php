<?php

namespace Armd\RegionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/** 
 * @ORM\Entity 
 * @ORM\Table(name="content_region") 
 */
class Region
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */    
    private $id;

    /**
     * @ORM\Column(type="string")
     */    
    private $title;
    
    /**
     * @ORM\Column(type="integer", nullable="true")
     */    
    private $code;
    
    /**
     * @ORM\Column(type="integer", nullable="true")
     */    
    private $okato;    

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
     * Set code
     *
     * @param integer $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set okato
     *
     * @param integer $okato
     */
    public function setOkato($okato)
    {
        $this->okato = $okato;
    }

    /**
     * Get okato
     *
     * @return integer 
     */
    public function getOkato()
    {
        return $this->okato;
    }
}
<?php

namespace Armd\CultureMapBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Armd\CultureMapBundle\Entity\Subject;

/**
 * Armd\CultureMapBundle\Entity\CultureObjectType
 *
 * @ORM\Table(name="mk_culture_object_type")
 * @ORM\Entity
 */
class CultureObjectType
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
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

}
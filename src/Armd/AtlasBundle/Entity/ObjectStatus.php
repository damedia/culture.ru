<?php

namespace Armd\AtlasBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Armd\AtlasBundle\Entity\ObjectStatus
 *
 * @ORM\Table(name="atlas_object_status")
 * @ORM\Entity(repositoryClass="Armd\AtlasBundle\Repository\ObjectRepository")
 */
class ObjectStatus
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(name="action_title", type="string", length=255, nullable=true)
     */
    private $actionTitle;

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
     * @return ObjectStatus
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
     * Set actionTitle
     *
     * @param string $actionTitle
     * @return ObjectStatus
     */
    public function setActionTitle($actionTitle)
    {
        $this->actionTitle = $actionTitle;
    
        return $this;
    }

    /**
     * Get actionTitle
     *
     * @return string 
     */
    public function getActionTitle()
    {
        return $this->actionTitle;
    }
}
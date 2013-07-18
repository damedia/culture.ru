<?php

namespace Armd\PersonBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DoctrineExtensions\Taggable\Taggable;
use Armd\MainBundle\Model\ChangeHistorySavableInterface;

/**
 * Armd\ExhibitBundle\Entity\Person
 *
 * @ORM\Entity()
 * @ORM\Table(name="armd_person")
 */
class Person implements Taggable, ChangeHistorySavableInterface
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
     * @var string $name
     *
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @var integer $century
     *
     * @ORM\Column(type="text")
     */
    private $description;
    
    /**
     * @var string $lifeDates
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $lifeDates;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\MediaBundle\Entity\Media", cascade={"all"}, fetch="EAGER")
     */
    private $image;
    
    /**
     * @ORM\ManyToMany(targetEntity="PersonType")
     * @ORM\JoinTable(name="person_person_type")
     * @ORM\OrderBy({"title" = "ASC"})
     */
    private $personTypes;  


    /**
     * @ORM\Column(name="corrected", type="boolean", nullable=true)
     */
    protected $corrected;       
    
    private $tags;

    public function __toString()
    {
        return $this->getName();
    }
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTags()
    {
        $this->tags = $this->tags ? : new ArrayCollection();

        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }
    
    /**
     * @return string
     */
    public function getTaggableType()
    {
        return 'armd_person';
    }
    
    /**
     * @return int
     */
    public function getTaggableId()
    {
        return $this->getId();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->personTypes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Person
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
     * Set description
     *
     * @param string $description
     * @return Person
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add personTypes
     *
     * @param \Armd\PersonBundle\Entity\PersonType $personTypes
     * @return Person
     */
    public function addPersonType(\Armd\PersonBundle\Entity\PersonType $personTypes)
    {
        $this->personTypes[] = $personTypes;
    
        return $this;
    }

    /**
     * Remove personTypes
     *
     * @param \Armd\PersonBundle\Entity\PersonType $personTypes
     */
    public function removePersonType(\Armd\PersonBundle\Entity\PersonType $personTypes)
    {
        $this->personTypes->removeElement($personTypes);
    }

    /**
     * Get personTypes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPersonTypes()
    {
        return $this->personTypes;
    }

    /**
     * Set lifeDates
     *
     * @param string $lifeDates
     * @return Person
     */
    public function setLifeDates($lifeDates)
    {
        $this->lifeDates = $lifeDates;
    
        return $this;
    }

    /**
     * Get lifeDates
     *
     * @return string 
     */
    public function getLifeDates()
    {
        return $this->lifeDates;
    }

    /**
     * Set image
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $image
     * @return Person
     */
    public function setImage(\Application\Sonata\MediaBundle\Entity\Media $image = null)
    {

        if (is_null($image) || $image->isUploaded()) {
            $this->image = $image;
        }

        return $this;
    }

    /**
     * Get image
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media 
     */
    public function getImage()
    {
        return $this->image;
    }
    
    /**
     * Set corrected
     *
     * @param boolean $corrected
     * @return Person
     */
    public function setCorrected($corrected)
    {
        $this->corrected = $corrected;
    
        return $this;
    }

    /**
     * Get corrected
     *
     * @return boolean 
     */
    public function getCorrected()
    {
        return $this->corrected;
    }

    public function getClassName()    
    {
        return get_class($this);
    }    
}
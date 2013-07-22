<?php

namespace Armd\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Armd\MainBundle\Entity\ChangeHistory
 *
 * @ORM\Table(name="content_change_history")
 * @ORM\Entity()
 */
class ChangeHistory 
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

   /**
    * @ORM\Column(type="string", nullable=false, name="entity_class")
    */
   protected $entityClass;
   
   /**
    * @ORM\Column(type="integer", nullable=false, name="entity_id")
    */
   protected $entityId;   
   
   /**
    * @ORM\Column(type="array", nullable=true, name="changes")
    */
   protected $changes;
   
   /**
    * @ORM\Column(type="datetime", nullable=false, name="updated_at")
    */
   protected $updatedAt;
   
    /**
     * @ORM\ManyToOne(targetEntity="Armd\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", nullable=true)
     */
   protected $updatedBy;
   
   /**
    * @ORM\Column(type="string", nullable=false, name="updated_ip")
    */
   protected $updatedIP;   
   

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
     * Set entityClass
     *
     * @param string $entityClass
     * @return ChangeHistory
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    
        return $this;
    }

    /**
     * Get entityClass
     *
     * @return string 
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set entityId
     *
     * @param integer $entityId
     * @return ChangeHistory
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    
        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set changes
     *
     * @param array $changes
     * @return ChangeHistory
     */
    public function setChanges($changes)
    {
        $this->changes = $changes;
    
        return $this;
    }

    /**
     * Get changes
     *
     * @return array 
     */
    public function getChanges()
    {
        return $this->changes;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return ChangeHistory
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedIP
     *
     * @param string $updatedIP
     * @return ChangeHistory
     */
    public function setUpdatedIP($updatedIP)
    {
        $this->updatedIP = $updatedIP;
    
        return $this;
    }

    /**
     * Get updatedIP
     *
     * @return string 
     */
    public function getUpdatedIP()
    {
        return $this->updatedIP;
    }

    /**
     * Set updatedBy
     *
     * @param \Armd\UserBundle\Entity\User $updatedBy
     * @return ChangeHistory
     */
    public function setUpdatedBy(\Armd\UserBundle\Entity\User $updatedBy = null)
    {
        $this->updatedBy = $updatedBy;
    
        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return \Armd\UserBundle\Entity\User 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
<?php

namespace Armd\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="armd_user_favorites")
 */
class Favorites
{
    const TYPE_ATLAS = 'atlas';
    const TYPE_MEDIA = 'media';
    const TYPE_LECTURE = 'lecture';
    const TYPE_MUSEUM_LESSON = 'museum_lesson';
    const TYPE_THEATER = 'theater';
    const TYPE_PERFORMANCE = 'performance';
    const TYPE_TOURIST_ROUTE = 'tourist_route';
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Armd\UserBundle\Entity\User", cascade={"all"})
     */
    protected $user;

    /**
     * @ORM\Column(type="string", name="resource_type")
     */
    protected $resourceType;
    
    /**
     * @ORM\Column(type="integer", name="resource_id")
     */
    protected $resourceId;

    /**
     * Set resourceType
     *
     * @param string $resourceType
     * @return Favorites
     */
    public function setResourceType($resourceType)
    {
        $this->resourceType = $resourceType;
    
        return $this;
    }

    /**
     * Get resourceType
     *
     * @return string 
     */
    public function getResourceType()
    {
        return $this->resourceType;
    }

    /**
     * Set resourceId
     *
     * @param integer $resourceId
     * @return Favorites
     */
    public function setResourceId($resourceId)
    {
        $this->resourceId = $resourceId;
    
        return $this;
    }

    /**
     * Get resourceId
     *
     * @return integer 
     */
    public function getResourceId()
    {
        return $this->resourceId;
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
     * Set user
     *
     * @param \Armd\UserBundle\Entity\User $user
     * @return Favorites
     */
    public function setUser(\Armd\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Armd\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
<?php

namespace Armd\UserBundle\Entity;

use Armd\UserBundle\Entity\User;

/**
 * Armd\UserBundle\Entity\ViewedContent
 */
class ViewedContent
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var \DateTime $date
     */
    protected $date;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var integer $entityId
     */
    protected $entityId;

    /**
     * @var string $entityClass
     */
    protected $entityClass;

    /**
     * @var string $entityClass
     */
    protected $entityTitle;

    /**
     * @var string $entityClass
     */
    protected $entityUrl;


    public function __construct()
    {
        $this->date = new \DateTime();
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
     * Get date
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     * @param \DateTime $date
     * @return ViewedContent
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get user
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     * @param User $user
     * @return ViewedContent
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get entityId
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set entityId
     * @param integer $entityId
     * @return ViewedContent
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityClass
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set entityClass
     * @param string $entityClass
     * @return ViewedContent
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get entityTitle
     * @return string
     */
    public function getEntityTitle()
    {
        return $this->entityTitle;
    }

    /**
     * Set entityTitle
     * @param string $entityTitle
     * @return ViewedContent
     */
    public function setEntityTitle($entityTitle)
    {
        $this->entityTitle = $entityTitle;

        return $this;
    }

    /**
     * Get entityUrl
     * @return string
     */
    public function getEntityUrl()
    {
        return $this->entityUrl;
    }

    /**
     * Set entityUrl
     * @param string $entityUrl
     * @return ViewedContent
     */
    public function setEntityUrl($entityUrl)
    {
        $this->entityUrl = $entityUrl;

        return $this;
    }
}

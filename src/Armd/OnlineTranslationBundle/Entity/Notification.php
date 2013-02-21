<?php

namespace Armd\OnlineTranslationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Armd\OnlineTranslationBundle\Entity\Notification
 *
 * @ORM\Entity
 * @ORM\Table(name="online_translation_notification", uniqueConstraints={@ORM\UniqueConstraint(name="notification_idx", columns={"onlinetranslation_id", "email", "period"})})
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $period;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Armd\OnlineTranslationBundle\Entity\OnlineTranslation")
     */
    private $onlineTranslation;

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
     * Set period
     *
     * @param integer $period
     * @return Notification
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    
        return $this;
    }

    /**
     * Get period
     *
     * @return integer 
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Notification
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set onlineTranslation
     *
     * @param \Armd\OnlineTranslationBundle\Entity\OnlineTranslation $onlineTranslation
     * @return Notification
     */
    public function setOnlineTranslation(\Armd\OnlineTranslationBundle\Entity\OnlineTranslation $onlineTranslation = null)
    {
        $this->onlineTranslation = $onlineTranslation;
    
        return $this;
    }

    /**
     * Get onlineTranslation
     *
     * @return \Armd\OnlineTranslationBundle\Entity\OnlineTranslation 
     */
    public function getOnlineTranslation()
    {
        return $this->onlineTranslation;
    }
}
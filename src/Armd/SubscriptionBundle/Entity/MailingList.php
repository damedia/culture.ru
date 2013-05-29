<?php

namespace Armd\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="subscription_mailing_list")
 */
class MailingList
{
    const TYPE_NEW_NEWS = 'new_news';
    const TYPE_NEW_CONTENT = 'new_content';
    const TYPE_CUSTOM = 'custom';
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    private $id;

    /**
     * Периодичны ли выпуски у рассылки? Или они создаются вручную?
     *
     * @todo Разбить на два флага...
     *
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool
     */
    private $periodically;

    /**
     * Название рассылки.
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $title;

    /**
     * Просто описание рассылки.
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $description;

    /**
     * "Подпись" к выпускам — текст от редакции, который будет автоматически включаться в каждый выпуск.
     *
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string
     */
    private $issueSignature;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $type;
    
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * 
     * @var boolean
     */
    private $enabled;
    
    /**
     * @ORM\ManyToMany(targetEntity="\Armd\UserBundle\Entity\User", mappedBy="subscriptions")
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $subscribers;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="mailingList")
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $issues;
    
    /**
     * Периодичность выхода выпусков в рассылке.
     *
     * @var \DateInterval
     */
    private $interval;

    public function __construct($title = '')
    {
        $this->title          = $title;
        $this->type           = self::TYPE_CUSTOM;
        $this->description    = '';
        $this->issueSignature = '';
        $this->periodically   = false;
        $this->enabled        = false;
        $this->interval       = new \DateInterval('P1D');
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param string $issueSignature
     */
    public function setIssueSignature($issueSignature)
    {
        $this->issueSignature = $issueSignature;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    /**
     * @param boolean $periodically
     */
    public function setPeriodically($periodically)
    {
        $this->periodically = $periodically;
    }
    
    /**
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return string
     */
    public function getIssueSignature()
    {
        return $this->issueSignature;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return \Armd\UserBundle\Entity\User[]
     */
    public function getSubscribers()
    {
        return $this->subscribers;
    }
    
    /**
     * @return boolean
     */
    public function isPeriodically()
    {
        return $this->periodically;
    }
    
    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}

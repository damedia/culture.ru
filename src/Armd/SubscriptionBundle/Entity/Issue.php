<?php

namespace Armd\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="subscription_issue")
 */
class Issue
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="MailingList", inversedBy="issues")
     * @ORM\JoinColumn(name="mailing_list_id", nullable=false)
     *
     * @var \Armd\SubscriptionBundle\Entity\MailingList
     */
    private $mailingList;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $sendedAt;

    /**
     * @param \Armd\SubscriptionBundle\Entity\MailingList $mailingList
     */
    public function __construct($mailingList = null)
    {
        $this->mailingList = $mailingList;

        $this->content = '';

        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Для админки (Sonata). По-хорошему, конечно, выпилить такой метод нужно.
     *
     * @param \Armd\SubscriptionBundle\Entity\MailingList $mailingList
     */
    public function setMailingList($mailingList)
    {
        $this->mailingList = $mailingList;
    }

    public function getSendedAt()
    {
        return $this->sendedAt;
    }

    /**
     * @return \Armd\SubscriptionBundle\Entity\MailingList
     */
    public function getMailingList()
    {
        return $this->mailingList;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setSendedAt($sendedAt)
    {
        $this->sendedAt = $sendedAt;
    }
}

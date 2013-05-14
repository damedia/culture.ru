<?php

namespace Armd\SubscriptionBundle\Service;

use Armd\SubscriptionBundle\Entity\Issue;
use Armd\SubscriptionBundle\Entity\MailingList;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class MailingListManager
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * @var \Armd\SubscriptionBundle\Service\ContentFillerFactory
     */
    private $contentFillerFactory;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $templateEngine;

    /**
     * @param \Doctrine\ORM\EntityManager                                $entityManager
     * @param \Swift_Mailer                                              $mailer
     * @param \Armd\SubscriptionBundle\Service\ContentFillerFactory      $contentFillerFactory
     * @param \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface $templateEngine
     */
    public function __construct($entityManager, $mailer, $contentFillerFactory, $templateEngine)
    {
        $this->entityManager        = $entityManager;
        $this->mailer               = $mailer;
        $this->contentFillerFactory = $contentFillerFactory;
        $this->templateEngine       = $templateEngine;
    }

    /**
     * Создаём выпуски для рассылок, какие можем. Если ничего не можем, ничего не создаём.
     *
     * Отправка производится в другом месте!
     *
     * @return void
     */
    public function createIssuesForPeriodicallyMailingLists()
    {
        /* @var $mailingLists \Armd\SubscriptionBundle\Entity\MailingList[] */
        $mailingLists = $this->entityManager->getRepository('ArmdSubscriptionBundle:MailingList')->createQueryBuilder('ml')
            ->where('ml.periodically = true')
            ->andWhere('ml.enabled = true')
            ->getQuery()
            ->getResult();

        foreach ($mailingLists as $mailingList) {
            if (!$this->isIssuedForYesterday($mailingList)) {
                $issue = new Issue($mailingList);

                $contentFillers = $this->contentFillerFactory->getContentFillersFor($mailingList);

                $parameters = array(
                    'mailingList' => $mailingList,
                );
                foreach ($contentFillers as $contentFiller) {
                    $parameters = array_merge(
                        $parameters,
                        $contentFiller->getContentFor($issue)
                    );
                }

                $content = $this->templateEngine->render(
                    'ArmdSubscriptionBundle:Mail:'.$mailingList->getType().'.txt.twig',
                    $parameters
                );

                $issue->setContent($content);

                $this->entityManager->persist($issue);
            }
        }

        $this->entityManager->flush();
    }

    private function isIssuedForYesterday($mailingList)
    {
        $yesterday = new \DateTime();

        $yesterdayStart = clone $yesterday;
        $yesterdayStart->setTime(0, 0, 0);

        $yesterdayEnd = clone $yesterday;
        $yesterdayEnd->setTime(23, 59, 59);

        $issue = $this->entityManager->getRepository('ArmdSubscriptionBundle:Issue')->createQueryBuilder('i')
            ->where('i.mailingList = :mailingList')
            ->andWhere('i.createdAt BETWEEN :todayStart AND :todayEnd')
            ->setParameter('todayStart', $yesterdayStart)
            ->setParameter('todayEnd', $yesterdayEnd)
            ->setParameter('mailingList', $mailingList)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();

        return !is_null($issue);
    }

    /**
     * Перед этим стоит создать новые выпуски, см. выше.
     *
     * @todo Чтобы этот метод можно было использовать в "многопоточном" приложении, его нужно доработать до стиля
     * очереди (SELECT FOR UPDATE и т.д.).
     *
     * @return \Armd\SubscriptionBundle\Entity\Issue|null
     */
    public function findIssueForSending()
    {
        $issue = $this->entityManager->getRepository('ArmdSubscriptionBundle:Issue')->createQueryBuilder('i')
            ->leftJoin('i.mailingList', 'ml', \Doctrine\ORM\Query\Expr\Join::ON)
            ->where('i.sendedAt IS NULL')
            ->andWhere('ml.enabled = true')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();

        return $issue;
    }

    /**
     * Если в середине работы этого метода произойдёт сбой, то будет плохо — рассылку получит только половина
     * пользователей.
     *
     * @todo Тречить отдельно отправку каждому подписчику, чтобы в случае сбоя можно было восстановиться
     * ровно с нужно момента.
     *
     * @param \Armd\SubscriptionBundle\Entity\Issue $issue
     */
    public function send(Issue $issue)
    {
        $subscribers = $issue->getMailingList()->getSubscribers();

        $mailTemplate = new \Swift_Message();

        // TODO Addresses.
        $mailTemplate->setFrom('no-reply@culture.ru');

        $mailTemplate->setSubject($issue->getMailingList()->getTitle());

        $mailTemplate->setBody(nl2br(htmlspecialchars_decode($issue->getContent())), 'text/html');

        foreach ($subscribers as $subscriber) {
            $mail = clone $mailTemplate;
            $mail->setTo($subscriber->getEmail());

            $this->mailer->send($mail);
        }

        $issue->setSendedAt(new \DateTime());

        $this->entityManager->persist($issue);
        $this->entityManager->flush();
    }
}

<?php

namespace Armd\OnlineTranslationBundle\Command\Cron;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class NotificationsSendCommand extends DoctrineCommand {
    protected $em;

    protected function configure() {
        $this->setName('broadcasts:notifications:send')
             ->setDescription('Sending email notifications');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        try {
            $container = $this->getContainer();
            $container->get('security.context')->setToken(new AnonymousToken(uniqid(), 'anon.', array()));

            $emailFrom = $container->getParameter('mail_from');
            $translator = $container->get('translator');
            $templating = $container->get('templating');
            $mailer = $container->get('mailer');

            $em = $this->getEntityManager('default');
            $notificationsRepository = $em->getRepository('ArmdOnlineTranslationBundle:Notification');
            $notifications = $notificationsRepository->findAll();

            foreach ($notifications as $notification) {
                $broadcast = $notification->getOnlineTranslation();
                $broadcastDate = $broadcast->getDate();
                $notificationPeriod = $notification->getPeriod();

                if ($this->itIsTimeToSendAnEmail($broadcastDate, $notificationPeriod)) {
                    $emailTo = $notification->getEmail();

                    $subject = $translator->trans('Mail.Subject') . $broadcast->getTitle();

                    $body = $templating->render('ArmdOnlineTranslationBundle:Default:email.html.twig', array(
                        'title' => $broadcast->getTitle(),
                        'date' => $broadcastDate->format('d m Y H:i'),
                        'location' => $broadcast->getLocation(),
                        'description' => $broadcast->getFullDescription()
                    ));

                    $message = \Swift_Message::newInstance()
                        ->setSubject($subject)
                        ->setFrom($emailFrom)
                        ->setTo($emailTo)
                        ->setBody($body)
                        ->setContentType("text/html");

                    $mailer->send($message);

                    $em->remove($notification);
                }
            }

            $em->flush();
        }
        catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    private function itIsTimeToSendAnEmail($broadcastDate, $notificationPeriod) {
        $now = new \DateTime();

        return ($broadcastDate->getTimestamp() - $notificationPeriod * 60 <= $now->getTimestamp());
    }
}
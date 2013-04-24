<?php

namespace Armd\OnlineTranslationBundle\Command\Cron;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class SendingNotificationCommand extends DoctrineCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('armd-mk:online-translation:sending-notifications')
            ->setDescription('Sending email notifications');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $em = $this->getEntityManager('default');       
            $entities = $em->getRepository('ArmdOnlineTranslationBundle:Notification')->findAll();

            foreach ($entities as $entity) {
                $now = new \Datetime();
                $translation = $entity->getOnlineTranslation();

                if ($translation->getDate()->getTimestamp() - $entity->getPeriod() * 60 <= $now->getTimestamp()) {
                    $emailFrom = $this->getContainer()->getParameter('mail_from');
                    $emailTo = $entity->getEmail();
                    $subject = 'Онлайн-трансляция #' . $translation->getTitle() . '# на портале Культура.рф';
                    $body = $this->getContainer()->get('templating')->render('ArmdOnlineTranslationBundle:Email:email.html.twig', array(
                        'title' => $translation->getTitle(),
                        'date' => $translation->getDate()->format('d m Y H:i'),
                        'location' => $translation->getLocation(),
                        'description' => $translation->getFullDescription()
                    ));
                    $message = \Swift_Message::newInstance()
                        ->setSubject($subject)
                        ->setFrom($emailFrom)
                        ->setTo($emailTo)
                        ->setBody($body)
                        ->setContentType("text/html")
                    ;
                    $this->getContainer()->get('mailer')->send($message);
                    $em->remove($entity);
                }              
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
        
        $em->flush();   
    }
}
<?php

namespace Armd\OnlineTranslationBundle\Command\Cron;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Armd\OnlineTranslationBundle\Entity\OnlineTranslation;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class BroadcastSetLiveCommand extends DoctrineCommand {
    protected $em;

    protected function configure() {
        $this->setName('broadcasts:set-live')
             ->setDescription('Changing active broadcast status to "online"');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $container->get('security.context')->setToken(new AnonymousToken(uniqid(), 'anon.', array()));

        $em = $this->getEntityManager('default');
        $broadcastsRepository = $em->getRepository('ArmdOnlineTranslationBundle:OnlineTranslation');
        $broadcast = $broadcastsRepository->findOneBy(array(
            'published' => true,
            'type' => OnlineTranslation::STATUS_ANNOUNCE
        ));

        if ($broadcast AND $this->itsTimeToGoLive($broadcast->getDate())) {
            $broadcast->setType(OnlineTranslation::STATUS_LIVE);

            $em->flush();
        }
    }

    private function itsTimeToGoLive($broadcastDate) {
        $now = new \DateTime();

        return (($broadcastDate->getTimestamp() - $now->getTimestamp()) <= 0);
    }
}
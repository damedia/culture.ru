<?php

namespace Armd\OnlineTranslationBundle\Command\Cron;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Armd\OnlineTranslationBundle\Entity\OnlineTranslation;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class BroadcastUnpublishCommand extends DoctrineCommand {
    protected $em;

    protected function configure() {
        $this->setName('broadcasts:unpublish')
             ->setDescription('Unpublish a broadcast after duration time has been exceeded');
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
            'type' => OnlineTranslation::STATUS_LIVE
        ));

        if ($broadcast->getDuration() != 0 AND $this->durationExceeded($broadcast->getDate(), $broadcast->getDuration())) {
            $broadcast->setType(OnlineTranslation::STATUS_ANNOUNCE);
            $broadcast->setPublished(false);

            $em->flush();
        }
    }

    private function durationExceeded($broadcastDate, $duration) {
        $now = new \DateTime();

        return (($broadcastDate->getTimestamp() + $duration * 60 - $now->getTimestamp()) <= 0);
    }
}
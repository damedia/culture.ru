<?php

namespace Armd\SubscriptionBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @author Alexey Shockov <alexey@shockov.com>
 */
class SendCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('armd-mk:subscription:send')
            ->setDescription('Create issues (if needed) and send pending mails for them to users.')
            ->setHelp('')

            // TODO --verbose flag for output. By default output nothing (will useful for cron).

            ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getMailingListManager();

        // Вынести в отдельную комунду?..
        $manager->createIssuesForPeriodicallyMailingLists();

        while ($issue = $manager->findIssueForSending()) {
            $manager->send($issue);
        }
    }

    /**
     * @return \Armd\SubscriptionBundle\Service\MailingListManager
     */
    private function getMailingListManager()
    {
        return $this->getContainer()->get('armd_subscription.mailing_list_manager');
    }
}

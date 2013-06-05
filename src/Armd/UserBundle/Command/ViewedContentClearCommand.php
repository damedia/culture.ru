<?php

namespace Armd\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class ViewedContentClearCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    /**
     * @var int
     */
    protected $oldRecordsOffset = 200;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('armd:user:viewed-content-clear')
            ->setDescription('Clears content viewed by users.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $em    = $this->getEntityManager();
        $users = $this->getDoctrine()->getRepository('ArmdUserBundle:User')->findAll();

        if ($users) {
            $output->writeln('Start clearing viewed content.');

            foreach ($users as $user) {
                $output->write('  ' .(string) $user. ': ');

                $oldRecords = $this->getDoctrine()->getRepository('ArmdUserBundle:ViewedContent')->findBy(
                    array('user' => $user),
                    array('date' => 'desc'),
                    null,
                    $this->oldRecordsOffset
                );

                if ($oldRecords) {
                    foreach ($oldRecords as $record) {
                        $em->remove($record);
                    }

                    $output->writeln('<fg=green>' .count($oldRecords) .' records removed.</fg=green>');

                } else {
                    $output->writeln('<fg=yellow>Nothing to remove.</fg=yellow>');
                }
            }

        } else {
            $output->writeln('  No users found.');
        }

        $output->writeln('Done.');

        $em->flush();
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        if (!$this->doctrine) {
            $this->doctrine = $this->getContainer()->get('doctrine');
        }

        return $this->doctrine;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getEntityManager()
    {
        if (!$this->em) {
            $this->em = $this->getDoctrine()->getEntityManager();
        }

        return $this->em;
    }
}

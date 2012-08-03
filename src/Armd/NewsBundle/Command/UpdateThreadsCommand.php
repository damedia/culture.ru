<?php

namespace Armd\NewsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

class UpdateThreadsCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('armd:comments:updateThreads')
            ->setDescription('Create comments Threads for not-thread news')
            ->setHelp(<<<EOT
Create comments Threads for not-thread news
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $newsList = $em->getRepository('Armd\NewsBundle\Entity\News')->findBy( array('thread' => null) );

        $output->writeln('Found '.count($newsList).' news');

        foreach($newsList as $news) {
            $thread = $this->getContainer()->get('fos_comment.manager.thread')->createThread();
            $thread->setPermalink('/');
            $this->getContainer()->get('fos_comment.manager.thread')->saveThread($thread);

            $news->setThread($thread);

            $em->persist($news);
            $em->flush();

        }

        $output->writeln('All done.');
    }

}

<?php

namespace Armd\CommunicationPlatformBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;

class InstallAcesCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('armd:cp:installAces')
            ->setDescription('Installs global ACEs')
            ->setDefinition(array(
                new InputOption('flush', null, InputOption::VALUE_NONE, 'Flush existing Acls'),
            ))
            ->setHelp(<<<EOT
This command should be run once during the installation process of the entire bundle or
after enabling Acl for the first time.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getContainer()->has('security.acl.provider')) {
            $output->writeln('You must setup the ACL system, see the Symfony2 documentation for how to do this.');

            return;
        }

        $builder = new MaskBuilder();

        $aclProvider = $this->getContainer()->get('security.acl.provider');
        $oid = new ObjectIdentity('class', 'Armd\CommunicationPlatformBundle\Entity\Proposals');

        try {
            $acl = $aclProvider->createAcl($oid);
        } catch (AclAlreadyExistsException $exists) {
            return;
        }

        if ($input->getOption('flush')) {
            $output->writeln('Flushing Global ACEs');

            $aclProvider->deleteAcl($oid);
        }


        $builder->add('iddqd');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_SUPER_ADMIN'), $builder->get());

        $builder->reset();
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'), $builder->get());

        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_USER'), $builder->get());

        $aclProvider->updateAcl($acl);

        $output->writeln('Global ACEs have been installed.');
    }
}

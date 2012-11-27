<?php

namespace Armd\AtlasBundle\Command\Temp;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Armd\AtlasBundle\Entity\Object;
use Armd\MuseumBundle\Entity\Museum;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class UpdateImageDescriptionsCommand extends DoctrineCommand
{
    protected function configure()
    {

        $this
            ->setName('armd-mk:atlas:update-image-descriptions')
            ->setDescription('')
            ->setHelp(
            <<<EOT
EOT
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager('default');

        $objects = $em->getRepository('ArmdAtlasBundle:Object')->findAll();
        $objectManager = $this->getContainer()->get('armd_atlas.manager.object');

        foreach ($objects as $object) {
            $output->writeln('Update description for atlas object ' . $object->getId());
            $objectManager->updateImageDescription($object);
        }
        $em->flush();
    }


}
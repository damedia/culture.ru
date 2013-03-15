<?php

namespace Armd\AtlasBundle\Command\Util;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class UpdateTechTagsCommand extends DoctrineCommand
{
    protected function configure()
    {
        $this
            ->setName('armd-mk:atlas:update-tech-tags')
            ->setDescription('Update tech tags');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Execution...');
        $em = $this->getEntityManager('default');
        $tagManager = $this->getContainer()->get('fpn_tag.tag_manager');
        $entities = $em->getRepository('ArmdAtlasBundle:Object')->findAll();
        
        foreach ($entities as $entity) {
            $tag = $tagManager->loadOrCreateTag('o' . $entity->getId(), false);
            $tag->setIsTechnical(true);
            $tagManager->addTag($tag, $entity);
            $tagManager->saveTagging($entity, false);              
        }
        
        $em->flush();      
        
        $output->writeln('<info>Update tech tags completed</info>');
    }


}
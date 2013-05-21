<?php

namespace Armd\LectureBundle\Command\Util;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class ImportBannersCommand extends DoctrineCommand
{
    protected function configure()
    {
        $this
            ->setName('armd-mk:lecture:import-banners')
            ->setDescription('Import vertical and horizontal banners');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Execution...');
        $em = $this->getEntityManager('default');
        $tagManager = $this->getContainer()->get('fpn_tag.tag_manager');
        $entities = $em->getRepository('ArmdLectureBundle:Lecture')->findAll();
        
        foreach ($entities as $entity) {
            $tag = $tagManager->loadOrCreateTag('l' . $entity->getId(), false);
            $tag->setIsTechnical(true);
            $tagManager->addTag($tag, $entity);
            $tagManager->saveTagging($entity, false);               
        }
        
        $em->flush();
        
        $output->writeln('<info>Update tech tags completed</info>');
    }


}
<?php
namespace Armd\TvigleVideoBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateMetadataCommand extends Command {
    protected function configure() {
        $this->setName('armd:tvigle:update')->setDescription('Update metadata from tvigle');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getApplication()->getKernel()->getContainer();
        $manager = $container->get('armd_tvigle_video.manager.tvigle_video');
        $em = $container->get('doctrine')->getManager();

        $videos = $em->getRepository('ArmdTvigleVideoBundle:TvigleVideo')->findAll();
        foreach($videos as $video) {
            $output->writeln('Update tvigle '.$video->getTvigleId());
            $manager->updateVideoDataFromTvigle($video);
        }

        $em->flush();
    }
}
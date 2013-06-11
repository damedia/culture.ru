<?php
namespace Armd\MuseumBundle\Command\Temp;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class WarArchiveJsonCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('armd:museum:war-archive-json')
            ->setDescription('Save directory as war archive json')
            ->addArgument('directory', InputArgument::REQUIRED)
            ->addArgument('jsonPath', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new Finder();

        $finder->directories()->in($input->getArgument('directory'));
        $result = array();
        foreach ($finder as $dir) {
            $dirinfo = pathinfo($dir->getRealpath());
            $finderFiles = new Finder();
            $finderFiles->files()->in($dir->getRealpath());
            $result[$dirinfo['basename']] = array();
            foreach ($finderFiles as $file) {
                $fileinfo = pathinfo($file->getRealpath());
                $result[$dirinfo['basename']][] = $fileinfo['basename'];
            }
        }
        file_put_contents($input->getArgument('jsonPath'), json_encode($result));

//        $container = $this->getApplication()->getKernel()->getContainer();
//        $manager = $container->get('armd_tvigle_video.manager.tvigle_video');
//        $em = $container->get('doctrine')->getManager();
//
//        $videos = $em->getRepository('ArmdTvigleVideoBundle:TvigleVideo')->findAll();
//        foreach($videos as $video) {
//            $output->writeln('Update tvigle ' . $video->getTvigleId());
//            $manager->updateVideoDataFromTvigle($video);
//        }
//
//        $em->flush();
    }
}
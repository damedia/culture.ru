<?php

namespace Armd\LectureBundle\Command\Util;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;

class ImportBannersCommand extends DoctrineCommand
{
    protected function configure()
    {
        $this
        ->setName('armd-mk:lecture:import-banners')
        ->setDescription('Import vertical and horizontal banners.')
        ->setDefinition(
                array(
                    new InputArgument('bannerDir', InputArgument::REQUIRED, 'Banner dir')
                )
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Execution...');

        $em = $this->getEntityManager('default');
        $bannerDir = $input->getArgument('bannerDir');

        $finder = new Finder();
        $finder->files()->in($bannerDir);

        foreach ($finder as $file) {
        }


        // vertical banner
        $bannerPath = $bannerDir . '/1_v.jpg';

        $media = new Media();
        $media->setBinaryContent($bannerPath);
        $media->setContext('lecture');;
        $media->setProviderName('sonata.media.provider.image');

        $lectures = $em->getRepository('ArmdLectureBundle:Lecture')->findAll();
        foreach ($lectures as $lecture) {
            $output->writeln('Set vertical banner for lecture ' . $lecture->getId());
            $lecture->setVerticalBanner($media);
            $em->persist($media);
        }

        $em->flush();


        // horizontal banner
        $bannerPath = $bannerDir . '/1_h.jpeg';

        $media = new Media();
        $media->setBinaryContent($bannerPath);
        $media->setContext('lecture');;
        $media->setProviderName('sonata.media.provider.image');

        foreach ($lectures as $lecture) {
            $output->writeln('Set horizontal banner for lecture ' . $lecture->getId());
            $lecture->setHorizontalBanner($media);
            $em->persist($media);
        }

        $em->flush();


        $output->writeln('<info>Finished</info>');
    }


}
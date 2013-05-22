<?php

namespace Armd\LectureBundle\Command\Util;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;

class ImportBannersCommand extends DoctrineCommand
{
    protected $input;
    protected $output;

    protected function configure()
    {
        $this
            ->setName('armd-mk:lecture:import-banners')
            ->setDescription('Import vertical and horizontal banners.')
            ->addOption('vertical-banner-dir', null, InputOption::VALUE_OPTIONAL, 'vertical banner dir')
            ->addOption('horizontal-banner-dir', null, InputOption::VALUE_OPTIONAL, 'horizontal banner dir')
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $output->writeln('Execution...');

        if ($input->getOption('vertical-banner-dir')) {
            $this->importBanners('verticalBanner', $input->getOption('vertical-banner-dir'));
        }

        if ($input->getOption('horizontal-banner-dir')) {
            $this->importBanners('horizontalBanner', $input->getOption('horizontal-banner-dir'));
        }

        $output->writeln('<info>Finished</info>');
    }


    protected function importBanners($fieldName, $bannerDir) {
        $this->output->writeln("Import banners for field '$fieldName' form path '$bannerDir'");

        $em = $this->getEntityManager('default');
        $lectureRepo = $em->getRepository('ArmdLectureBundle:Lecture');

        $finder = new Finder();
        $finder->files()->in($bannerDir);

        foreach ($finder as $file) {
            $pathinfo = pathinfo($file->getRealPath());

            $lectureId = $pathinfo['filename'];
            if ((int) $lectureId  > 0) {
                $imagePath = $file->getRealPath();

                $media = new Media();
                $media->setBinaryContent($imagePath);
                $media->setContext('lecture');
                $media->setProviderName('sonata.media.provider.image');

                $lecture = $lectureRepo->find($lectureId);
                if ($lecture) {
                    $this->output->writeln("Import $fieldName for $lectureId  from $imagePath");
                    $method = 'set' . ucfirst($fieldName);
                    $lecture->$method($media);
                    $em->persist($lecture);
                    $em->flush();
                }
            }
        }


        $this->output->writeln("Import banners for field '$fieldName' finished");
    }

}
<?php

namespace Armd\AtlasBundle\Command\Util;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Armd\AtlasBundle\Entity\Object;
use Armd\MuseumBundle\Entity\Museum;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class LoadSideBannersCommand extends DoctrineCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('armd-mk:atlas:load-side-banners')
            ->setDescription('Loads side banners')
            ->setDefinition(array(
                new InputArgument('imagesDirectory', InputArgument::REQUIRED, 'The path where images <atlas_object_id>.jpg reside')
            ))
            ->setHelp(
            <<<EOT
            The <info>armd-mk:atlas:load-side-banners</info> loads.
<info>php app/console armd-mk:atlas:load-side-banners</info>
EOT
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager('default');
        $mediaHelper = $this->getContainer()->get('armd_media_helper.media_helper');

        $imagesDir = $input->getArgument('imagesDirectory');
        if (substr($imagesDir, -1) !== '/' && substr($imagesDir, -1) !== '\\') {
            $imagesDir .= '/';
        }

        $files = scandir($imagesDir);
        foreach($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $output->writeln('Process file ' . $file);

            $parts = explode('.', $file);
            $objectId = (int) $parts[0];
            if ($objectId < 1) {
                continue;
            }

            $object = $em->getRepository('ArmdAtlasBundle:Object')->find($objectId);
            if (empty($object)) {
                $output->writeln('Skip file ' . $file . ' (cant find object with such id)');
                continue;
            }

            $imagePath = $imagesDir . $file;
            $media = $mediaHelper->createMediaImage($imagePath, 'atlas');
            if (empty($media)) {
                $output->writeln('Skip file ' . $file . ' (cant create media)');
                continue;
            }

            $object->setSideBannerImage($media);
            $em->flush();
        }

    }


}
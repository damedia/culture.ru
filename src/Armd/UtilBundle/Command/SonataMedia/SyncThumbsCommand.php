<?php

/*
 * This file is part of the Sonata package.
*
* (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Armd\UtilBundle\Command\SonataMedia;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Sonata\MediaBundle\Command\BaseCommand;

/**
 * This command can be used to re-generate the thumbnails for all uploaded medias.
 *
 * Useful if you have existing media content and added new formats.
 *
 */
class SyncThumbsCommand extends BaseCommand
{
    protected $quiet = false;
    protected $output;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('armdutil:media:sync-thumbnails')
            ->setDescription('Sync uploaded image thumbs with new media formats')
            ->setDefinition(array(
                new InputArgument('providerName', InputArgument::OPTIONAL, 'The provider'),
                new InputArgument('context', InputArgument::OPTIONAL, 'The context'),
                new InputArgument('mediaId', InputArgument::OPTIONAL, 'Media ID'),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $provider = $input->getArgument('providerName');
        $context  = $input->getArgument('context');
        $mediaId =  $input->getArgument('mediaId');

        $this->quiet = $input->getOption('quiet');
        $this->output = $output;

        $filter = array();
        if (!empty($provider)) {
            $filter['providerName'] = $provider;
        }
        if (!empty($context)) {
            $filter['context'] = $context;
        }
        if (!empty($mediaId)) {
            $filter['id'] = $mediaId;
        }

        $medias = $this->getMediaManager()->findBy($filter);

        $this->log(sprintf("Loaded %s medias for generating thumbs (provider: %s, context: %s)", count($medias), $provider, $context));

        foreach ($medias as $media) {
            $provider = $this->getMediaPool()->getProvider($media->getProviderName());

            $referenceFile = $provider->getReferenceFile($media, 'reference');
            if ($referenceFile->exists()) {
                $this->log("Generating thumbs for " . $media->getName() . ' - ' . $media->getId());
                $provider->removeThumbnails($media);
                $provider->generateThumbnails($media);
            } else {
                $this->log("Skip thumbs creation for " . $media->getName() . ' - ' . $media->getId() . ' (reference file does not exists)');
            }

        }

        $this->log('Done.');
    }

    /**
     * Write a message to the output
     *
     * @param string $message
     *
     * @return void
     */
    protected function log($message)
    {
        if ($this->quiet === false) {
            $this->output->writeln($message);
        }
    }
}

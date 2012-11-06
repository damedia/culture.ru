<?php

namespace Armd\AtlasBundle\Command\Temp;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Armd\AtlasBundle\Entity\Object;
use Armd\MuseumBundle\Entity\Museum;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class MoveVirtualTourImagesCommand extends DoctrineCommand
{
    protected $em;

    protected function configure()
    {

        $this
            ->setName('armd-mk:atlas:move-virtual-tour-images')
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

        $mediaPool = $this->getContainer()->get('sonata.media.pool');
        $museums = $em->getRepository('ArmdMuseumBundle:Museum')->findAll();
        foreach ($museums as $museum) {
            /** @var $image \Application\Sonata\MediaBundle\Entity\Media */
            $image = $museum->getImage();
            if($image instanceof \Application\Sonata\MediaBundle\Entity\Media) {
                $provider = $mediaPool->getProvider($image->getProviderName());
                $path = $provider->getReferenceImage($image);
                if(!$provider->getFilesystem()->has($path)) {
                    $output->writeln('Exists ' . $path);
                } else {
                    $output->writeln('Not exists ' . $path);

                }
            }
        }
    }


}
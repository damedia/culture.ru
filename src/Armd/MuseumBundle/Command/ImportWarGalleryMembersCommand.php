<?php

namespace Armd\MuseumBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Finder\Finder;

use Armd\MuseumBundle\Entity\WarGalleryMember;
use Application\Sonata\MediaBundle\Entity\Media;

class ImportWarGalleryMembersCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;

    protected $headers = array(
        'Награды',
        'Происхождение',
        'Источники и литература'
    );

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('armd:museum:import-war-gallery-members')
            ->setDescription('Import War Gallery members')
            ->setDefinition(array(
                new InputArgument('dir', InputArgument::REQUIRED, 'Data path')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');

        $finder = new Finder();
        $finder->files()->in($dir .'/txt_ru')->name('*.txt')->sortByName();

        $output->writeln('Importing:');
        
        foreach ($finder as $file) {
            $name        = null;
            $years       = null;
            $ranks       = null;
            $preview     = null;
            $image       = null;
            $description = null;

            if (preg_match('/^(.+)\s+\((.+гг\.)\)\s+([^\n]+)(.*)/us', $file->getContents(), $matches)) {
                $name        = trim($matches[1]);
                $years       = trim($matches[2]);
                $ranks       = trim($matches[3]);
                
                $rows = explode("\n", trim($matches[4]));

                foreach ($rows as $k=>$row) {
                    $row = trim($row);

                    if ($row) {
                        if (preg_match('/^(' .implode('|', $this->headers) .'):?$/', $row, $matches)) {
                            $row = '<h3>' .$matches[1] .'</h3>';

                        } else {
                            $row = '<p>' .$row .'</p>';
                        }

                        $rows[$k] = $row;

                    } else {
                        unset($rows[$k]);
                    }
                }

                $description = implode("\n", $rows);
            }
            
            if ($name !== null) {
                $warGalleryMember = new WarGalleryMember();
                $warGalleryMember->setName($name);
                $warGalleryMember->setYears($years);
                $warGalleryMember->setRanks($ranks);
                $warGalleryMember->setDescription($description);

                $previewFinder = new Finder();
                $previewFinder->files()->in($dir .'/photo_SMALL/')->name(
                    preg_replace(
                        '/^(\d+)_.+/',
                        '$1_wgallery_pre',
                        $file->getFilename()
                    ) .'*'
                );
                
                if ($previewFinder->count()) {
                    $files        = iterator_to_array($previewFinder);
                    $previewPath  = array_shift($files);
                    
                    $previewMedia = new Media();
                    $previewMedia->setProviderName('sonata.media.provider.image');
                    $previewMedia->setContext('war_gallery');
                    $previewMedia->setBinaryContent($previewPath);

                    $this->getContainer()
                        ->get('sonata.media.manager.media')
                            ->save($previewMedia);

                    $warGalleryMember->setPreview($previewMedia);
                }

                $imageFinder = new Finder();
                $imageFinder->files()->in($dir .'/photo_BIG/')->name(
                    preg_replace(
                        '/^(\d+)_.+/',
                        '$1_wgallery',
                        $file->getFilename()
                    ) .'*'
                );
                
                if ($imageFinder->count()) {
                    $files      = iterator_to_array($imageFinder);
                    $imagePath  = array_shift($files);

                    $imageMedia = new Media();
                    $imageMedia->setProviderName('sonata.media.provider.image');
                    $imageMedia->setContext('war_gallery');
                    $imageMedia->setBinaryContent($imagePath);

                    $this->getContainer()
                        ->get('sonata.media.manager.media')
                            ->save($imageMedia);

                    $warGalleryMember->setImage($imageMedia);
                }

                $em = $this->getEntityManager();
                $em->persist($warGalleryMember);
                $em->flush();

                $output->writeln('  <fg=green>' .$warGalleryMember->getName() .'</fg=green>');
            }
        }

        $output->writeln('Done.');
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected function getDoctrine()
    {
        if (!$this->doctrine) {
            $this->doctrine = $this->getContainer()->get('doctrine');
        }

        return $this->doctrine;
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectManager
     */
    protected function getEntityManager()
    {
        if (!$this->em) {
            $this->em = $this->getDoctrine()->getEntityManager();
        }

        return $this->em;
    }
}

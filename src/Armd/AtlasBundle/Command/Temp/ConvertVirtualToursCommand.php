<?php

namespace Armd\AtlasBundle\Command\Temp;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Armd\AtlasBundle\Entity\Object;
use Armd\MuseumBundle\Entity\Museum;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class ConvertVirtualToursCommand extends DoctrineCommand
{
    protected $em;

    protected function configure()
    {

        $this
            ->setName('armd-mk:atlas:convert-virtual-tours')
            ->setDescription('Convert virtual tours in atlas object to virtual tour links (Museum entity)')
            ->setHelp(
            <<<EOT
            The <info>armd-mk:atlas:convert-virtual-tours</info> converts virtual tours in atlas object into links to Museum.
<info>php app/console armd-mk:atlas:convert-virtual-tours</info>
EOT
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager('default');

        $objects = $em->getRepository('ArmdAtlasBundle:Object')->findAll();
        $museumsFoundCount = 0;
        $museumsCreatedCount = 0;
        foreach ($objects as $object) {
            if(strlen($object->getVirtualTour()) > 0) {
                $museum = $this->getMuseumByLink($object->getVirtualTour());
                if (empty($museum)) {
                    $output->writeln('Create virtual tour from object ' . $object->getVirtualTour());
                    $museumsCreatedCount++;
                    $museum = $this->createMuseumFromAtlasObject($object);
                } else {
                    $output->writeln('Virtual museum found ' . $object->getVirtualTour());
                    $museumsFoundCount++;
                }
                if (!empty($museum)) {
                    $object->addVirtualTour($museum);
                }
            }
        }
        $em->flush();
        $output->writeln(sprintf('Created virtual tours: %s. Found virtual tours: %s',  $museumsCreatedCount, $museumsFoundCount));
    }

    protected function getMuseumByLink($url) {
        $em = $this->getEntityManager('default');

        $url = trim($url);
        $museum = $em->getRepository('ArmdMuseumBundle:Museum')->createQueryBuilder('m')
            ->where('m.url LIKE :url')
            ->setParameter('url', '%' . $url . '%')
            ->getQuery()
            ->getOneOrNullResult();

        return $museum;
    }

    protected function createMuseumFromAtlasObject(Object $object)
    {
        $em = $this->getEntityManager('default');

        $image = $object->getVirtualTourImage();

        $museum = new Museum();
        if($image) {
            $image->setContext('museum');
            $museum->setImage($image);
        }
        $museum->setPublished(true);
        $museum->setUrl($object->getVirtualTour());
        $museum->setTitle($object->getTitle());
        $em->persist($museum);
        $em->flush();

        return $museum;
    }

}
<?php

namespace Armd\AtlasBundle\Command\Util;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Armd\AtlasBundle\Entity\Object;
use Armd\MuseumBundle\Entity\Museum;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class CsvExportRussiaImagesCommand extends DoctrineCommand
{
    protected function configure()
    {
        $this
            ->setName('armd-mk:atlas:csv-export-russia-images')
            ->setDescription('Export russia images')
            ->setDefinition(array(
                new InputArgument('outputFile', InputArgument::REQUIRED, 'Export file path')
            ));
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager('default');

        $objects = $em->getRepository('ArmdAtlasBundle:Object')->createQueryBuilder('o')
            ->where('o.showAtRussianImage = TRUE')
            ->select('o.title, o.id')
            ->orderBy('o.title', 'ASC')
            ->getQuery()
            ->getScalarResult();

        $file = fopen($input->getArgument('outputFile'), 'w+');
        if ($file) {
            $i = 1;
            foreach ($objects as $object) {
                $url = 'http://culture.ru' . $this->getContainer()->get('router')->generate(
                        'armd_atlas_default_object_view',
                        array('id' => $object['id'])
                    );
                $line = $i++ . "\t" . $object['title'] . "\t" . $url . "\n";
                fwrite($file, $line);
            }
            fclose($file);
        } else {
            throw new \RuntimeException('Cant open file ' . $input->getArgument('outputFile'));
        }
    }

}
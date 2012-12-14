<?php

namespace Armd\ChronicleBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class ExportEventsCommand extends DoctrineCommand
{
    protected function configure()
    {

        $this
            ->setName('armd-mk:chronicle:export-events')
            ->setDescription('Export chronicle events')
            ->setHelp(
            <<<EOT
            The <info>armd-mk:atlas:convert-virtual-tours</info> converts virtual tours in atlas object into links to Museum.
<info>php app/console armd-mk:atlas:convert-virtual-tours</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager('default');
        $serializer = $this->getContainer()->get('serializer');
        $mediaPool = $this->getContainer()->get('sonata.media.pool');

        $outputDir = realpath(__DIR__ . '/../../../..') . '/tmp/chronicle_events';
        if (!is_dir($outputDir)) {
            throw new \Exception('ERROR: output dir ' . $outputDir . ' doesn\'t exists');
        }

        $events = $em->getRepository('ArmdChronicleBundle:Event')->findBy(array(), null, null);
        $imagesById = array();
        foreach($events as $event) {
            $image  = $event->getImage();
            if($image) {
                $imagesById[$image->getId()] = $image;
            }
        }


        $data = json_decode($serializer->serialize($events, 'json'));
        foreach ($data as $dataItem)
        {
            if (!empty($dataItem->image->id)) {
                $dataItem->image->referenceImage = 'http://culture.ru/ru/uploads/media/'
                    . $mediaPool->getProvider($dataItem->image->provider_name)
                    ->getReferenceImage($imagesById[$dataItem->image->id]);
            }
        }

        file_put_contents($outputDir . '/events.json', json_encode($data));

        $output->writeln('Export complete. Exported: ' . count($events) . ' events');
    }

    protected function createPath($path)
    {
        if (is_dir($path)) {
            return true;
        }
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1);
        $return = createPath($prev_path);

        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
    }
}
<?php

namespace Armd\LectureBundle\Command\Util;

use Doctrine\Bundle\DoctrineBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputArgument;
use Armd\AtlasBundle\Entity\Object;
use Armd\MuseumBundle\Entity\Museum;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;

class CsvExportLecturesCommand extends DoctrineCommand
{
    protected function configure()
    {
        $this
            ->setName('armd-mk:lecture:csv-export')
            ->setDescription('Export lectures')
            ->setDefinition(array(
                new InputArgument('lectureSuperTypeCode', InputArgument::REQUIRED, 'Lecture super type code'),
                new InputArgument('outputFile', InputArgument::REQUIRED, 'Export file path')
            ));
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager('default');

        $objects = $em->getRepository('ArmdLectureBundle:Lecture')->createQueryBuilder('l')
                   ->where('l.published = TRUE')
                   ->innerJoin('l.lectureSuperType', 'st')
                   ->select('l.title, l.id')
                   ->where('st.code = :lecture_super_type')
                   ->orderBy('l.title', 'ASC')
                   ->setParameter('lecture_super_type', $input->getArgument('lectureSuperTypeCode'))
                   ->getQuery()
                   ->getScalarResult();

        $file = fopen($input->getArgument('outputFile'), 'w+');
        if ($file) {
            $i = 1;
            foreach ($objects as $object) {
                $url = 'http://culture.ru' . $this->getContainer()->get('router')->generate(
                        'armd_lecture_view',
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
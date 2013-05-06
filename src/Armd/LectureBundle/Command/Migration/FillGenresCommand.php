<?php
namespace Armd\LectureBundle\Command\Migration;

use Armd\LectureBundle\Entity\LectureCategory;
use Armd\LectureBundle\Entity\LectureManager;
use Armd\LectureBundle\Entity\LectureSuperType;
use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FillGenresCommand  extends DoctrineCommand {
    protected function configure()
    {
        $this
            ->setName('armd-mk:lecture:migration-fill-genres')
            ->setDescription(
                'Fill genres based on categories'
                . ' Need this because lack of well supported ContainerAware migrations.'
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {


    }


}
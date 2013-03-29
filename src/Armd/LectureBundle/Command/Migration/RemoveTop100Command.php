<?php
namespace Armd\LectureBundle\Command\Migration;

use Armd\LectureBundle\Entity\LectureCategory;
use Armd\LectureBundle\Entity\LectureManager;
use Armd\LectureBundle\Entity\LectureSuperType;
use Armd\UtilBundle\NestedSet\TreeRepairer;
use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveTop100Command  extends DoctrineCommand {
    protected function configure()
    {
        $this
            ->setName('armd-mk:lecture:remove-top-100')
            ->setDescription(
                'Remove top100 category and supertype. Use isTop100 checkbox'
                . ' Need this because lack of well supported ContainerAware migrations.'
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->removeTop100();
        $this->repairCategoryTree();
    }

    protected function removeTop100()
    {
        $em = $this->getEntityManager('default');
        $lectureManager = $this->getContainer()->get('armd_lecture.manager.lecture');

        $cinemaSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode('LECTURE_SUPER_TYPE_CINEMA');
        $topSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode('LECTURE_SUPER_TYPE_TOP100');
        $topCategory = $cinemaCategory = $em->getRepository('ArmdLectureBundle:LectureCategory')
            ->findOneByLectureSuperType($topSuperType);


        $cinemaCategory = $em->getRepository('ArmdLectureBundle:LectureCategory')
            ->findOneByTitle('Кинофильмы');

        $lectures = $em->createQueryBuilder()
            ->select('l')
            ->from('ArmdLectureBundle:Lecture', 'l')
            ->where('l.lectureSuperType = :super_type')
            ->setParameter('super_type', $topSuperType)
            ->getQuery()->getResult();

        foreach ($lectures as $lecture) {
            $lecture->setLectureSuperType($cinemaSuperType);
            $newCategories = array();
            foreach($lecture->getCategories() as $category) {
                $found = false;
                foreach($cinemaCategory->getChildren() as $newCategory) {
                    if ($category->getTitle() === $newCategory->getTitle()) {
                        $newCategories[] = $newCategory;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $category->setParent($cinemaCategory);
                    $em->flush();
                    $newCategories[] = $category;
                }
            }
            $lecture->setCategories($newCategories);
            $lecture->setIsTop100Film(true);
        }

        $cinemaCategory->setSystemSlug('CINEMA_TOP_100');

        $em->remove($topCategory);
        $em->remove($topSuperType);

        $topCategory = $em->getRepository('ArmdLectureBundle:LectureCategory')
            ->findOneByTitle('100 фильмов для школьников');
        $em->remove($topCategory);

        $em->flush();

        $this->repairCategoryTree();

    }

    protected function repairCategoryTree() {
        $em = $this->getEntityManager('default');
//        $em->getRepository('ArmdLectureBundle:LectureCategory')->recover();
        $repairer = new TreeRepairer();
        $repairer->rebuildTree($em, $em->getRepository('ArmdLectureBundle:LectureCategory'));

    }


}
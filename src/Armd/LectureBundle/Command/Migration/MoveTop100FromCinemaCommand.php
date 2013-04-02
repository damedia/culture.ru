<?php
namespace Armd\LectureBundle\Command\Migration;

use Armd\LectureBundle\Entity\LectureCategory;
use Armd\LectureBundle\Entity\LectureManager;
use Armd\LectureBundle\Entity\LectureSuperType;
use Armd\UtilBundle\NestedSet\TreeRepairer;
use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MoveTop100FromCinemaCommand  extends DoctrineCommand {
    protected function configure()
    {
        $this
            ->setName('armd-mk:lecture:migration-move-top100-from-cinema')
            ->setDescription(
                'Moves top100 films to cinema supertype.'
                . ' Need this because lack of well supported ContainerAware migrations.'
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getEntityManager('default');

        $lectureManager = $this->getContainer()->get('armd_lecture.manager.lecture');

        $cinemaTop100Category = $em->getRepository('ArmdLectureBundle:LectureCategory')
            ->findOneByTitle('100 фильмов для школьников');
        $top100SuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode('LECTURE_SUPER_TYPE_TOP100');
        $top100RootCategory = $em->getRepository('ArmdLectureBundle:LectureCategory')
            ->findOneByLectureSuperType($top100SuperType);



        $lectures = $lectureManager->findObjects(
            array(
                LectureManager::CRITERIA_CATEGORY_ID_OR_PARENT_ID => $cinemaTop100Category->getId()
            )
        );

        foreach ($lectures as $lecture) {
            $lecture->setLectureSuperType($top100SuperType);
            $newCategories = array();
            foreach($lecture->getCategories() as $category) {
                foreach($top100RootCategory->getChildren() as $newCategory) {
                    if ($category->getTitle() === $newCategory->getTitle()) {
                        $newCategories[] = $newCategory;
                    }   
                }
            }
            $lecture->setCategories($newCategories);
        }

        $em->remove($cinemaTop100Category);
        $em->flush();
    }

//    /**
//     * {@inheritDoc}
//     */
//    protected function execute(InputInterface $input, OutputInterface $output)
//    {
//        $em = $this->getEntityManager('default');
//
//        // create super type
//        $superType = new LectureSuperType();
//        $superType->setCode('LECTURE_SUPER_TYPE_TOP100');
//        $superType->setName('100 фильмов для школьников');
//        $em->persist($superType);
//        $em->flush();
//
//        // create root category
//        $rootCategory = $em->getRepository('ArmdLectureBundle:LectureCategory')
//            ->findOneByTitle('=== Корневая категория (100 лучших фильмов) ===');
//        $rootCategory->setLectureSuperType($superType);
//        $em->persist($rootCategory);
//        $em->flush();
//
//        // move categories
//        $category = $em->getRepository('ArmdLectureBundle:LectureCategory')
//            ->findOneByTitle('100 фильмов для школьников');
//        foreach($category->getChildren() as $childCategory) {
//            $childCategory->setParent($rootCategory);
//            $em->persist($childCategory);
//            foreach($childCategory->getLectures() as $lecture) {
//                $lecture->setLectureSuperType($superType);
//            }
//            $em->flush();
//        }
//        $em->remove($category);
//        $em->flush();
//
//    }
}
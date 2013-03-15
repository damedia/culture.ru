<?php
namespace Armd\LectureBundle\Command\Migration;

use Armd\LectureBundle\Entity\LectureCategory;
use Armd\UtilBundle\NestedSet\TreeRepairer;
use Doctrine\Bundle\MigrationsBundle\Command\DoctrineCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MoveTop100ToCinemaCommand  extends DoctrineCommand {
    protected function configure()
    {
        $this
            ->setName('armd-mk:lecture:migration-move-top100-to-cinema')
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
        $this->repairCategoryTree();
        $this->moveCategories();
        $this->changeSuperType();
        $this->removeThematics();
        $this->removeTop100SuperType();
    }

    protected function repairCategoryTree() {
        $em = $this->getEntityManager('default');
        $em->getRepository('ArmdLectureBundle:LectureCategory')->recover();
//        $repairer = new TreeRepairer();
//        $repairer->rebuildTree($em, $em->getRepository('ArmdLectureBundle:LectureCategory'));

    }

    protected function changeSuperType() {
        $em = $this->getEntityManager('default');

        $cinemaSuperType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode('LECTURE_SUPER_TYPE_CINEMA');


        $lectures = $em->createQueryBuilder()
            ->select('l')
            ->from('ArmdLectureBundle:Lecture', 'l')
            ->innerJoin('l.lectureSuperType', 'st')
            ->where("st.code = 'LECTURE_SUPER_TYPE_TOP100'")
            ->getQuery()->getResult();

        /** @var Armd\LectureBundle\Entity\Lecture $lecture */
        foreach($lectures as $lecture) {
            $lecture->setLectureSuperType($cinemaSuperType);
            $em->persist($lecture);
        }

        $em->flush();
    }

    protected function moveCategories() {
        $em = $this->getEntityManager('default');

        $rootTop100Category = $em
            ->createQueryBuilder()
            ->select('c')
            ->from('ArmdLectureBundle:LectureCategory', 'c')
            ->innerJoin('c.lectureSuperType', 'st')
            ->where("st.code = 'LECTURE_SUPER_TYPE_TOP100'")
            ->andWhere('c.parent IS NULL')
            ->getQuery()->getSingleResult();

        $rootCinemaCategory =  $em
                    ->createQueryBuilder()
                    ->select('c')
                    ->from('ArmdLectureBundle:LectureCategory', 'c')
                    ->innerJoin('c.lectureSuperType', 'st')
                    ->where("st.code = 'LECTURE_SUPER_TYPE_CINEMA'")
                    ->andWhere('c.parent IS NULL')
                    ->getQuery()->getSingleResult();

        $cinemaTop100Category = $em->getRepository('ArmdLectureBundle:LectureCategory')
            ->findOneByTitle('100 фильмов для школьников');

        if (!$cinemaTop100Category) {
            $cinemaTop100Category = new LectureCategory();
            $cinemaTop100Category->setTitle('100 фильмов для школьников');
            $cinemaTop100Category->setParent($rootCinemaCategory);
            $em->persist($cinemaTop100Category);
        }

        foreach($rootTop100Category->getChildren() as $top100Category) {
            $top100Category->setParent($cinemaTop100Category);
        }

        $em->flush();

    }

    protected function removeThematics()
    {
        $em = $this->getEntityManager('default');
        $thematicCategories = $em->getRepository('ArmdLectureBundle:LectureCategory')->findByTitle('Тематика');

        foreach ($thematicCategories as $category) {
            foreach($category->getChildren() as $children) {
                $children->setParent($category->getParent());
            }
            $em->flush();
            $em->remove($category);
        }
        $em->flush();
    }

    protected function removeTop100SuperType()
    {
        $em = $this->getEntityManager('default');

        $superType = $em->getRepository('ArmdLectureBundle:LectureSuperType')
            ->findOneByCode('LECTURE_SUPER_TYPE_TOP100');

        $em->getRepository('ArmdLectureBundle:LectureCategory')
            ->createQueryBuilder('c')
            ->update('ArmdLectureBundle:LectureCategory', 'c')
            ->set('c.lectureSuperType', 'null')
            ->where('c.lectureSuperType = :super_type')
            ->setParameter('super_type', $superType)
            ->getQuery()->execute();

        $em->remove($superType);
        $em->flush();

    }

}
<?php

namespace Armd\LectureBundle\Repository;

use \Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Armd\LectureBundle\Entity\LectureSuperType;
use Knp\Component\Pager\Paginator;

class LectureRepository extends EntityRepository
{

    public function findRecommended(LectureSuperType $superType, $limit = 4)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.recommended = TRUE')
            ->andWhere('l.lectureSuperType = :superType')
            ->andWhere('l.published = TRUE')
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameters(array(
            'superType' => $superType
        ));

        $this->makeQueryBuilderEager($qb);
        $recommendedLectures = $qb->getQuery()->getResult();

        return $recommendedLectures;
    }

    public function findLastAdded(LectureSuperType $superType, $limit = 2)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.lectureSuperType = :superType')
            ->andWhere('l.published = TRUE')
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameters(array(
            'superType' => $superType
        ));

        $this->makeQueryBuilderEager($qb);

        $lastAdded = $qb->getQuery()->getResult();
        return $lastAdded;
    }

    public function getFilterQueryBuilder($alias, $superType, $typeIds = null, $categoryIds = null, $sortBy = 'date')
    {
        $qb = $this->createQueryBuilder($alias)
            ->innerJoin($alias . '.categories', 'c')
            ->where($alias . '.lectureSuperType = :superType')->setParameter('superType', $superType)
            ->andWhere('l.published = TRUE');

        // filter by types
        if ($typeIds !== 'all') {
            $qb->andWhere('l.lectureType IN (:lecture_type_ids)')
                ->setParameter('lecture_type_ids', $typeIds);
        }

        // filter by categories
        if ($categoryIds !== 'all') {
            $qb->andWhere('c IN (:category_ids)')
                ->setParameter('category_ids', $categoryIds);
        }

        // sort
        if ($sortBy === 'date') {
            $qb->orderBy('l.createdAt', 'DESC');
        }
        elseif ($sortBy === 'comments') {

        }

        $this->makeQueryBuilderEager($qb);

        return $qb;
    }

    public function getTypeCategories(LectureSuperType $superType)
    {
        $types = $this->_em->getRepository('ArmdLectureBundle:LectureType')
            ->findAll();

        $result = array();
        foreach ($types as $type) {
            $categories = $this->_em->createQueryBuilder()
                ->select('c')
                ->from('ArmdLectureBundle:LectureCategory', 'c')
                ->innerJoin('c.lectures', 'l')
                ->where('l.lectureType = :type')
                ->andWhere('l.lectureSuperType = :superType')
                ->andWhere('l.published = TRUE')
                ->groupBy('c')
                ->setParameters(array(
                'type' => $type,
                'superType' => $superType
            ))
                ->getQuery()->getResult();

            $result[$type->getId()] = array();
            foreach ($categories as $category) {
                $result[$type->getId()][] = $category->getId();
            }
        }

        return $result;
    }

    protected function makeQueryBuilderEager(QueryBuilder $qb)
    {
        $rootAliases = $qb->getRootAliases();
        $rootAlias = $rootAliases[0];

        // categories join removed due to strange behaviour - some records disappear
        // i thing this is doctrine bug
        $qb
//            ->addSelect('_categories')
            ->addSelect('_lecture_video')
            ->addSelect('_lecture_super_type')
            ->addSelect('_lecture_video_image_media')
//            ->leftJoin($rootAlias . '.categories', '_categories')
            ->leftJoin($rootAlias . '.lectureVideo', '_lecture_video')
            ->leftJoin($rootAlias . '.lectureSuperType', '_lecture_super_type')
            ->leftJoin('_lecture_video.imageMedia', '_lecture_video_image_media')
        ;
    }
}
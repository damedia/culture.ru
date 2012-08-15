<?php

namespace Armd\LectureBundle\Repository;

use \Doctrine\ORM\EntityRepository;

class LectureRepository extends EntityRepository
{

    public function findRecommended($limit = 4)
    {
        $recommendedLectures = $this->createQueryBuilder('l')
            ->where('l.recommended = TRUE')
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();

        return $recommendedLectures;
    }

    public function findLastAdded($limit = 2)
    {
        $lastAdded = $this->createQueryBuilder('l')
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();

        return $lastAdded;
    }

    public function getFilterQueryBuilder($typeId, $categoryIds)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.lectureType = :lecture_type_id')
            ->andWhere('l.categories IN (:category_ids)')
            ->setParameters(
            array(
                 'lecture_type_id' => $typeId,
                 'category_ids' => $categoryIds
            ));

        return $qb;
    }
}
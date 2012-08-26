<?php

namespace Armd\LectureBundle\Repository;

use \Doctrine\ORM\EntityRepository;
use Armd\LectureBundle\Entity\LectureSuperType;
use Knp\Component\Pager\Paginator;

class LectureRepository extends EntityRepository
{

    public function findRecommended(LectureSuperType $superType, $limit = 4)
    {
        $recommendedLectures = $this->createQueryBuilder('l')
            ->where('l.recommended = TRUE')
            ->andWhere('l.lectureSuperType = :superType')
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameters(array(
                'superType' => $superType
            ))
            ->getQuery()->getResult();

        return $recommendedLectures;
    }

    public function findLastAdded(LectureSuperType $superType, $limit = 2)
    {
        $lastAdded = $this->createQueryBuilder('l')
            ->where('l.lectureSuperType = :superType')
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameters(array(
                'superType' => $superType
            ))
            ->getQuery()->getResult();

        return $lastAdded;
    }

    public function getFilterQueryBuilder($alias, $superType, $typeIds = null, $categoryIds = null, $sortBy = 'date')
    {
        $qb = $this->createQueryBuilder($alias)
            ->innerJoin($alias.'.categories', 'c')
            ->where($alias.'.lectureSuperType = :superType')->setParameter('superType', $superType)
        ;

        // filter by types
        if($typeIds !== 'all') {
            $qb->andWhere('l.lectureType IN (:lecture_type_ids)')
                ->setParameter('lecture_type_ids', $typeIds);
        }

        // filter by categories
        if($categoryIds !== 'all') {
            $qb->andWhere('c IN (:category_ids)')
            ->setParameter('category_ids', $categoryIds);
        }

        // sort
        if ($sortBy === 'date') {
            $qb->orderBy('l.createdAt', 'DESC');
        } elseif ($sortBy === 'comments') {

        }

        return $qb;
    }



    public function getTypeCategories(LectureSuperType $superType)
    {
        $types = $this->_em->getRepository('ArmdLectureBundle:LectureType')
            ->findAll();

        $result = array();
        foreach($types as $type) {
            $categories = $this->_em->createQueryBuilder()
                ->select('c')
                ->from('ArmdLectureBundle:LectureCategory', 'c')
                ->innerJoin('c.lectures', 'l')
                ->where('l.lectureType = :type')
                ->andWhere('l.lectureSuperType = :superType')
                ->groupBy('c')
                ->setParameters(array(
                    'type' =>  $type,
                    'superType' => $superType
                ))
                ->getQuery()->getResult();

            $result[$type->getId()] = array();
            foreach($categories as $category) {
                $result[$type->getId()][] = $category->getId();
            }
        }

        return $result;
    }
}
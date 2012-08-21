<?php

namespace Armd\LectureBundle\Repository;

use \Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\Paginator;

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

    public function getFilterQueryBuilder($alias, $typeIds = null, $categoryIds = null, $sortBy = 'date')
    {
        $qb = $this->createQueryBuilder($alias)
            ->innerJoin($alias.'.categories', 'c')
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



    public function getTypeCategories()
    {
        $types = $this->_em->getRepository('ArmdLectureBundle:LectureType')
            ->findAll();

        $result = array();
        foreach($types as $type) {
            $categories = $this->_em->createQueryBuilder()
                ->select('c')
                ->from('ArmdLectureBundle:LectureCategory', 'c')
                ->innerJoin('c.lectures', 'l')
                ->where('l.lectureType = :type')->setParameter('type', $type)
                ->groupBy('c')
                ->getQuery()->getResult();

            $result[$type->getId()] = array();
            foreach($categories as $category) {
                $result[$type->getId()][] = $category->getId();
            }
        }
        \gFuncs::dbgWriteLogVar($result, false, 'getTypeCategories'); // DBG:

        return $result;
    }
}
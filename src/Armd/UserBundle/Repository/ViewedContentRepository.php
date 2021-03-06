<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Valentin
 * Date: 07.08.13
 * Time: 18:25
 * To change this template use File | Settings | File Templates.
 */

namespace Armd\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ViewedContentRepository extends EntityRepository {
    public function getBlogStatsByIds($ids)
    {
        if (empty($ids)) {
            return array();
        }
        $stats = array_combine($ids, array_fill(0, count($ids), 0));
        $qb = $this->createQueryBuilder('a');
        foreach ($qb->select(array('a.entityId', 'COUNT(a)'))
                     ->where('a.entityClass = :class')
                     ->setParameter('class', 'ArmdBlogBundle:Blog')
                     ->andWhere($qb->expr()->in('a.entityId', $ids))
                     ->groupBy('a.entityId')
                     ->getQuery()
                     ->getArrayResult() as $row) {
            $stats[$row['entityId']] = $row[1];
        }

        return $stats;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getTopPerfomances($limit = 10) {
        $ids = array();
        foreach ($this->createQueryBuilder('a')
                     ->select(array('a.entityId', 'COUNT(a) as cnt'))
                     ->where('a.entityClass = :class')
                     ->setParameter('class', 'ArmdPerfomanceBundle:Perfomance')
                     ->groupBy('a.entityId')
                     ->orderBy('cnt', 'DESC')
                     ->setMaxResults($limit)
                     ->getQuery()
                     ->getArrayResult() as $row) {
            $ids[] = $row['entityId'];
        }

        return $ids;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getTopLectures($limit = 10) {
        $ids = array();
        $qb = $this->createQueryBuilder('a');

        $query = $qb
            ->from('ArmdLectureBundle:Lecture', 'l')
            ->select(array('a.entityId', 'COUNT(a) as cnt'))
            ->where('a.entityClass = :class')
            ->andWhere('a.entityId = l.id')
            ->andWhere('l.lectureSuperType = :superType')
            ->setParameter('superType', 1)
            ->setParameter('class', 'ArmdLectureBundle:Lecture')
            ->groupBy('a.entityId')
            ->orderBy('cnt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery();

        foreach ($query->getArrayResult() as $row) {
            $ids[] = $row['entityId'];
        }

        return $ids;
    }
}
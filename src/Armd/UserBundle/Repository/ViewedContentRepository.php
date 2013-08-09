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

class ViewedContentRepository extends EntityRepository
{
    public function getBlogStatsByIds($ids)
    {
        $stats = array_combine($ids, array_fill(0, count($ids), 0));
        $qb = $this->createQueryBuilder('a');
        foreach($qb->select(array('a.entityId', 'COUNT(a)'))
                    ->where('a.entityClass = :class')
                    ->setParameter('class', 'ArmdBlogBundle:Blog')
                    ->andWhere($qb->expr()->in('a.entityId', $ids))
                    ->groupBy('a.entityId')
                    ->getQuery()
                    ->getArrayResult() as $row)
        {
            $stats[$row['entityId']] = $row[1];
        }
        return $stats;
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getTopRussianImages($limit = 10)
    {
        $ids = array();
        foreach($this->createQueryBuilder('a')
            ->select(array('a.entityId', 'COUNT(a) as cnt'))
            ->where('a.entityClass = :class')
            ->setParameter('class', 'ArmdAtlasBundle:Object')
            ->groupBy('a.entityId')
            ->orderBy('cnt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult() as $row) {
            $ids[] = $row['entityId'];
        }
        return $ids;
    }
}
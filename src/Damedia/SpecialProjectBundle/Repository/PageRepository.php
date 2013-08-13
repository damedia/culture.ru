<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Valentin
 * Date: 07.08.13
 * Time: 18:25
 * To change this template use File | Settings | File Templates.
 */

namespace Damedia\SpecialProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PageRepository extends EntityRepository
{
    /**
     * @param string $date
     * @param int $limit
     * @return array
     */
    public function findForMainPage($date = '', $limit = 5)
    {
        $dt = new \DateTime();
        $dt->setTime(0, 0, 0);
        if ($date != '') {
            $tmp = explode('-', $date);
            $dt->setDate($tmp[0], $tmp[1], $tmp[2]);
        }

        return $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->where('p.isPublished = TRUE')
            ->andWhere('p.parent IS NULL')
            ->andWhere('p.showOnMain = TRUE')
            ->andWhere('p.showOnMainFrom <= :dt')
            ->andWhere('p.showOnMainTo >= :dt')
            ->setParameter('dt', $dt)
            ->orderBy('p.showOnMainTo')
            ->getQuery()
            ->getArrayResult();
    }
}
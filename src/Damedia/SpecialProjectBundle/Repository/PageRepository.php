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
    public function findForMainPage($limit = 5)
    {
        return $this->createQueryBuilder('p')
            ->setMaxResults($limit)
            ->where('p.isPublished = TRUE')
            ->andWhere('p.parent IS NULL')
            ->andWhere('p.showOnMain = TRUE')
            ->andWhere('p.showOnMainFrom <= :dt')
            ->andWhere('p.showOnMainTo >= :dt')
            ->setParameter('dt', new \DateTime())
            ->orderBy('p.showOnMainTo')
            ->getQuery()
            ->getArrayResult();
    }
}
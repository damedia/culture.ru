<?php

namespace Armd\ActualInfoBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class ActualInfoRepository extends EntityRepository
{
    public function findForMainPage($date)
    {
        $dt = new \DateTime();
        $dt->setTime(0, 0, 0);
        if ($date != '') {
            $tmp = explode('-', $date);
            $dt->setDate($tmp[0], $tmp[1], $tmp[2]);
        }

        try{
            $qb = $this->createQueryBuilder('a');

            return $qb->where('a.showOnMain = :show')
                ->setParameter('show', true)
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->andX(
                            'a.showOnMainTo IS NULL',
                            'a.showOnMainFrom IS NULL'
                        ),
                        $qb->expr()->andX(
                            'a.showOnMainFrom <= :dt',
                            'a.showOnMainTo IS NULL'
                        ),
                        $qb->expr()->andX(
                            'a.showOnMainFrom IS NULL',
                            'a.showOnMainTo >= :dt'
                        ),
                        $qb->expr()->andX('a.showOnMainFrom <= :dt', 'a.showOnMainTo >= :dt')
                    )
                )
                ->setParameter('dt', $dt)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch(NoResultException $e){
            return null;
        }
    }
}
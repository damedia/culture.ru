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
                ->andWhere('a.showOnMainFrom <= :dt1')
                ->andWhere($qb->expr()->orX('a.showOnMainTo > :dt1', 'a.showOnMainTo IS NULL'))
                ->setParameter('dt1', $dt)
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch(NoResultException $e){
            return null;
        }
    }
}
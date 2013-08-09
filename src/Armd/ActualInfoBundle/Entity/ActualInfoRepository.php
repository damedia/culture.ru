<?php

namespace Armd\ActualInfoBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class ActualInfoRepository extends EntityRepository
{
    public function findForMainPage()
    {
        try{
            return $this->createQueryBuilder('a')
                ->where('a.showOnMain = :show')
                ->setParameter('show', true)
                ->andWhere('a.showOnMainFrom <= :dt1')
                ->andWhere('a.showOnMainTo > :dt1')
                ->setParameter('dt1', new \DateTime())
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch(NoResultException $e){
            return null;
        }
    }
}
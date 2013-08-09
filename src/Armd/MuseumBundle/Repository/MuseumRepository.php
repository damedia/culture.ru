<?php

namespace Armd\MuseumBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class MuseumRepository extends EntityRepository
{
    public function findForMain()
    {
        try{
            return $this->createQueryBuilder('a')
                ->where('a.showOnMain = :show')
                ->setParameter('show', true)
                ->andWhere('a.showOnMainFrom <= :dt1')
                ->andWhere('a.showOnMainTo > :dt1')
                ->setParameter('dt1', new \DateTime())
                ->setMaxResults(1)
                ->orderBy('a.showOnMainOrd')
                ->getQuery()
                ->getSingleResult();
        } catch(NoResultException $e){
            return null;
        }
    }
}
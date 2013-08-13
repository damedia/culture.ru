<?php

namespace Armd\MuseumBundle\Repository;

use Armd\MuseumBundle\Entity\Museum;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class MuseumRepository extends EntityRepository
{
    /**
     * @param string $date
     * @return Museum|null
     */
    public function findForMain($date = '')
    {
        $dt = new \DateTime();
        $dt->setTime(0, 0, 0);
        if ($date != '') {
            $tmp = explode('-', $date);
            $dt->setDate($tmp[0], $tmp[1], $tmp[2]);
        }

        try{
            return $this->createQueryBuilder('a')
                ->where('a.showOnMain = :show')
                ->setParameter('show', true)
                ->andWhere('a.showOnMainFrom <= :dt1')
                ->andWhere('a.showOnMainTo > :dt1')
                ->setParameter('dt1', $dt)
                ->setMaxResults(1)
                ->orderBy('a.showOnMainOrd')
                ->getQuery()
                ->getSingleResult();
        } catch(NoResultException $e){
            return null;
        }
    }
}
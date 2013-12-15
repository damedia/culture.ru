<?php

namespace Armd\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class BlogRepository extends EntityRepository
{


    public function getPostsByUser($user = null)
    {
        $qb = $this->createQueryBuilder('b');

        if (null != $user) {
            $qb->where('b.user = :user')
                ->setParameter('user', $user);
        }

        $qb->orderBy('b.created_at', 'DESC');

        return $qb->getQuery()->execute();
    }

    public function getLastPostsByUser($user, $count = 3)
    {

        $qb = $this->createQueryBuilder('b');
        $qb->andWhere('b.user = :user')
            ->setMaxResults($count)
            ->setParameter('user', $user);

        $query = $qb->getQuery();

        return $query->execute();
    }

    public function getLast()
    {
        try{
            return $this->createQueryBuilder('b')
                ->orderBy('b.created_at', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleResult();
        } catch(NoResultException $e){
            return null;
        }
    }
}
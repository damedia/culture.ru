<?php

namespace Armd\BlogBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BlogRepository extends EntityRepository
{

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
        return $this->createQueryBuilder('b')
            ->orderBy('b.created_at', 'DESC')
            ->setMaxResults(1)
            ->getQuery()->getSingleResult();
    }
}
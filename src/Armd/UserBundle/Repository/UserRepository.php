<?php
namespace Armd\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAllActiveUsers()
    {
        return $this->getEntityManager()->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->from('ArmdUserBundle:User', 'u')
            ->where('u.lastActivity > :lastActivity')
            ->setParameter('lastActivity', new \DateTime(date(DATE_ATOM, time() - 60 * 10)))
            ->getQuery()->getSingleScalarResult();
    }

    public function findUserRoles($role=false)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('u')
            ->select('u, g')
            ->from('ArmdUserBundle:User', 'u')
            ->leftJoin('u.groups', 'g');
        if ($role) {
            $qb->where("g.roles LIKE :role")
               ->setParameter('role', '%'.$role.'%');
        }
        return $qb->getQuery()->execute();
    }

}

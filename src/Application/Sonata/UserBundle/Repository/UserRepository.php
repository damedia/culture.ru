<?php
namespace Application\Sonata\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAllActiveUsers()
    {
        return $this->getEntityManager()->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->from('ApplicationSonataUserBundle:User', 'u')
            ->where('u.lastActivity > :lastActivity')
            ->setParameter('lastActivity', new \DateTime(date(DATE_ATOM, time() - 60 * 10)))
            ->getQuery()->getSingleScalarResult();
    }

    public function findUserRoles()
    {
        return $this->getEntityManager()->createQueryBuilder('u')
            ->select('u, g')
            ->from('ApplicationSonataUserBundle:User', 'u')
            ->leftJoin('u.groups', 'g')
            ->getQuery()->execute();
    }
}
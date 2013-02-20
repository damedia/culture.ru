<?php

namespace Armd\UserBundle\Entity;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserManager extends BaseUserManager
{
    public function getCountOnlineUsers()
    {
        return $this->repository->findAllActiveUsers();
    }

    /**
     * Refreshed a user by User Instance
     *
     * Throws UnsupportedUserException if a User Instance is given which is not
     * managed by this UserManager (so another Manager could try managing it)
     *
     * It is strongly discouraged to use this method manually as it bypasses
     * all ACL checks.
     *
     * @param SecurityUserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(SecurityUserInterface $user)
    {
        $class = $this->getClass();
        if (get_class($user) != $class) {
            throw new UnsupportedUserException('Account is not supported.');
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function getUsersRoles()
    {
        return $this->repository->findUserRoles();
    }

    public function getModerators()
    {
        return $this->repository->findUserRoles('ROLE_MODERATOR');
    }

}

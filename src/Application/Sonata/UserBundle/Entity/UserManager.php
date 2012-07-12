<?php

namespace Application\Sonata\UserBundle\Entity;

use FOS\UserBundle\Entity\UserManager as BaseUserManager;

class UserManager extends BaseUserManager
{
    public function getCountOnlineUsers()
    {
        return $this->repository->findAllActiveUsers();
    }
}

<?php

namespace Armd\SocialAuthBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractSocialToken extends AbstractToken {

    /** @var $response Response */
    public $response;

    public function getRoles()
    {
        if($this->getUser() instanceof UserInterface) {
            $roles = array();
            foreach($this->getUser()->getRoles() as $role) {
                if (is_string($role)) {
                    $role = new Role($role);
                } elseif (!$role instanceof RoleInterface) {
                    throw new \InvalidArgumentException(sprintf('$roles must be an array of strings, or RoleInterface instances, but got %s.', gettype($role)));
                }
                $roles[] = $role;
            }
            return $roles;
        } else {
            return parent::getRoles();
        }
    }

}

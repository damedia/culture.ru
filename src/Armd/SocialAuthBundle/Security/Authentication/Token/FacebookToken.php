<?php

namespace Armd\SocialAuthBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class FacebookToken extends AbstractToken {

    public $accessCode;
    public $accessState;

    public $accessToken;
    public $accessTokenDateTime;
    public $accessTokenExpires;
    public $accessTokenUserId;

    public $facebookUserData;

    public function __construct(array $roles = array(), $accessCode = null, $accessState = null)
    {
        parent::__construct($roles);
        $this->accessCode = $accessCode;
        $this->accessState = $accessState;
    }

    /**
     * Returns the user credentials.
     *
     * @return mixed The user credentials
     */
    public function getCredentials()
    {
        return '';
    }

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

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->accessToken,
            $this->accessTokenDateTime,
            $this->accessTokenExpires,
            $this->accessTokenUserId,
            parent::serialize()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->accessToken,
            $this->accessTokenDateTime,
            $this->accessTokenExpires,
            $this->accessTokenUserId,
            $parentStr
        ) = unserialize($serialized);
        parent::unserialize($parentStr);
    }
}
<?php

namespace Armd\SocialAuthBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\RoleInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class VkontakteToken extends AbstractSocialToken {

    public $accessCode;

    public $accessToken;
    public $accessTokenDateTime;
    public $accessTokenExpiresIn;
    public $accessTokenUserId;

    public $vkUserData;

    public function __construct(array $roles = array(), $accessCode = null)
    {
        parent::__construct($roles);
        $this->accessCode = $accessCode;
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


    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->accessToken,
            $this->accessTokenDateTime,
            $this->accessTokenExpiresIn,
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
            $this->accessTokenExpiresIn,
            $this->accessTokenUserId,
            $parentStr
        ) = unserialize($serialized);
        parent::unserialize($parentStr);
    }
}
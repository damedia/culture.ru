<?php

namespace Armd\SocialAuthBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class TwitterToken extends AbstractSocialToken
{
    public $oauthToken;
    public $oauthTokenSecret;


    public function __construct(array $roles = array())
    {
        parent::__construct($roles);
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
}
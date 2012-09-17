<?php

namespace Armd\SocialAuthBundle\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class TwitterToken extends AbstractSocialToken
{
    public $oauthToken;
    public $oauthTokenSecret;
    public $oauthVerifier;
    public $twitterUserId;
    public $twitterUserData;

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

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            $this->oauthToken,
            $this->oauthTokenSecret,
            $this->oauthVerifier,
            $this->twitterUserId,
            $this->twitterUserData,
            parent::serialize()
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list(
            $this->oauthToken,
            $this->oauthTokenSecret,
            $this->oauthVerifier,
            $this->twitterUserId,
            $this->twitterUserData,
            $parentStr
        ) = unserialize($serialized);
        parent::unserialize($parentStr);
    }
}
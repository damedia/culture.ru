<?php

namespace Zim32\LoginzaBundle\DependencyInjection\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginzaToken extends AbstractToken {
    private $providerKey = 'main';
    private $credentials = NULL;

    public function __construct(array $roles){
        if(!isset($roles['ROLE_LOGINZA_USER'])) $roles[] = 'ROLE_LOGINZA_USER';
        parent::__construct($roles);
    }

    public function getCredentials(){
        return array();
    }

    public function getUid(){
        $info = $this->getAttribute('loginza_info');
        if(!isset($info['uid'])) throw new \Exception("User id is empty");
        return $info['uid'];
    }

    private function hasUserChanged(UserInterface $user)
    {
        return false;
    }
}

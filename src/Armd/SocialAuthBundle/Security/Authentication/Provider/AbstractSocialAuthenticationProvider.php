<?php

namespace Armd\SocialAuthBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use FOS\UserBundle\Entity\UserManager;

abstract class AbstractSocialAuthenticationProvider implements AuthenticationProviderInterface
{
    protected $router;
    protected $paramsReader;
    protected $em;
    protected $userManager;

    public function __construct(
            Router $router,
            AuthenticationProviderParametersReader $paramsReader,
            EntityManager $em,
            UserManager $userManager
    )
    {
        $this->router = $router;
        $this->paramsReader = $paramsReader;
        $this->em = $em;
        $this->userManager = $userManager;
    }


    public function curlRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // times out after 4s
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch); // run the whole process
        if ($result === false) {
            throw new \Exception('Curl error');
        }
        curl_close($ch);
        return $result;
    }

}
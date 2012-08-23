<?php

namespace Armd\UserBundle\Security;

use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LogoutSuccessHandler extends DefaultLogoutSuccessHandler implements ContainerAwareInterface
{
    protected $container;
    
    function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    function onLogoutSuccess(Request $request)
    {
        $response = parent::onLogoutSuccess($request);
        $this->clearCookies($response);
        
        return $response;
    }
    
    function clearCookies(Response $response)
    {
        $domain = $this->container->getParameter('domain');
        
        $response->headers->clearCookie('_USER_ID', '/', $domain);
        $response->headers->clearCookie('_USER_HASH', '/', $domain);
    }
}

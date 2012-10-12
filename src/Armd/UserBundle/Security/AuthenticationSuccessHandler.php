<?php

namespace Armd\UserBundle\Security;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use FOS\UserBundle\Model\UserInterface;


class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler implements ContainerAwareInterface
{
    protected $container;
    
    function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        $response = parent::onAuthenticationSuccess($request, $token);
        
        if ($token->getUser() instanceof UserInterface) {
            $this->setCookies($response, $token->getUser());
            $this->addRedirect($request, $response);
        }
        
        return $response;
    }
    
    function setCookies(Response $response, UserInterface $user)
    {
        $expire = new \DateTime();
        $expire->add(new \DateInterval('P1Y'));
        
        $domain = $this->container->getParameter('domain');
        
        $response->headers->setCookie(new Cookie('_USER_ID', $user->getId(), $expire, '/', $domain));
        $response->headers->setCookie(new Cookie('_USER_HASH', $user->getPassword(), $expire, '/', $domain));   
    }

    function addRedirect(Request $request, Response $response)
    {
        $redirectUrl = $request->getSession()->get('armd_user.post_auth_redirect');
        if (!empty($redirectUrl)) {
            $response->headers->set('Location', $redirectUrl);
        }
    }
}

<?php
namespace Armd\SocialAuthBundle\Security\Firewall;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;

abstract class AbstractSocialAuthenticationListener implements ListenerInterface
{
    protected $authenticationManager;
    protected $securityContext;
    protected $logger;

    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager,
        Logger $logger
    )
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->logger = $logger;
    }

    public function authenticateToken(GetResponseEvent $event, TokenInterface $token)
    {
        try {
            $returnValue = $this->authenticationManager->authenticate($token);

            if ($returnValue instanceof TokenInterface) {
                return $this->securityContext->setToken($returnValue);
            } elseif ($returnValue instanceof Response) {
                return $event->setResponse($returnValue);
            }
        } catch (AuthenticationException $e) {
            $this->logger->err($e->getMessage());
        }
    }
}
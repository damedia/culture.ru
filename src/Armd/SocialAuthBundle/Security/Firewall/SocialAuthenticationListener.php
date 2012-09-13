<?php

namespace Armd\SocialAuthBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Armd\SocialAuthBundle\Security\Authentication\Token\VkontakteToken;
use Armd\SocialAuthBundle\Security\Authentication\Token\FacebookToken;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;

class SocialAuthenticationListener implements ListenerInterface //extends AbstractAuthenticationListener
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

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     * @throws \InvalidArgumentException
     * @return void
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if($request->query->has('armd_social_auth_provider')) {
            switch ($request->get('armd_social_auth_provider')) {
                case 'vkontakte':
                    return  $this->authenticateVkontakte($event);
                    break;
                case 'facebook':
                    return  $this->authenticateFacebook($event);
                    break;
                default:
                    throw new \InvalidArgumentException('Unknown auth_provider');
            }
        }
    }

    public function authenticateVkontakte(GetResponseEvent $event)
    {
        if($event->getRequest()->query->has('code')) {
            $token = new VkontakteToken(
                array(),
                $event->getRequest()->get('code')
            );
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

    public function authenticateFacebook(GetResponseEvent $event)
    {
        if($event->getRequest()->query->has('code'))
        {
            $token = new FacebookToken(
                array(),
                $event->getRequest()->get('code'),
                $event->getRequest()->get('state')
            );
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

}
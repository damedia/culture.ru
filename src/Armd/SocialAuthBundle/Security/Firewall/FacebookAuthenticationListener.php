<?php

namespace Armd\SocialAuthBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Armd\SocialAuthBundle\Security\Authentication\Token\FacebookToken;

class FacebookAuthenticationListener extends AbstractSocialAuthenticationListener
{

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     * @return void
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->get('armd_social_auth_provider') === 'facebook') {
            if ($request->query->has('code')) {
                $token = $this->securityContext->getToken();
                $token->accessCode = $request->query->get('code');
            }
            else {
                $facebookState = substr(md5(rand(1, 1000) . microtime()), 0, 15);
                $token = new FacebookToken(array(), $facebookState);
            }

            $this->authenticateToken($event, $token);

        }
    }

}
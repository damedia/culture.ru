<?php

namespace Armd\SocialAuthBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Armd\SocialAuthBundle\Security\Authentication\Token\TwitterToken;

class TwitterAuthenticationListener extends AbstractSocialAuthenticationListener
{

    /**
     * This interface must be implemented by firewall listeners.
     *
     * @param GetResponseEvent $event
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if($request->get('armd_social_auth_provider') === 'twitter') {
            $token = new TwitterToken();
            $this->authenticateToken($event, $token);
        } else {

        }
    }

}
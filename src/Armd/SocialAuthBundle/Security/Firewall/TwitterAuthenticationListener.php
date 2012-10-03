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
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     * @return void
     */
    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->get('armd_social_auth_provider') === 'twitter') {
            if ($request->query->has('oauth_token') && $request->query->has('oauth_verifier')) {

                $token = $this->securityContext->getToken();
                if (!empty($token->oauthToken)) {

                    if ($request->get('oauth_token') != $token->oauthToken) {
                        throw new AuthenticationException('oauth_tokens don\'t match');
                    }

                    $token->oauthVerifier = $request->get('oauth_verifier');
                    $this->authenticateToken($event, $token);
                }

            } else {
                $token = new TwitterToken();
                $this->authenticateToken($event, $token);
            }
        }
    }

}
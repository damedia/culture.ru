<?php
namespace Armd\UserBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class PostAuthRedirectListener {

    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $redirectUrl = $request->get('post_auth_redirect');
        if(!empty($redirectUrl)) {
            $request->getSession()->set('armd_user.post_auth_redirect', $redirectUrl);
        }
    }
}
<?php

namespace Armd\SocialAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/auth-links", name="armd_social_auth_auth_links")
     * @Template()
     */
    public function authLinksAction()
    {
        $request = $this->getRequest();
        $request->getSession()->set(
            'armd_social_auth.post_auth_redirect',
            $request->get('redirectUrl', $this->getRequest()->getRequestUri())
        );
    }


    /**
     * @Route("/auth-result", name="armd_social_auth_auth_result")
     * @Template()
     */
    public function authResultAction()
    {
        $session = $this->getRequest()->getSession();
        if($session->has('armd_social_auth.post_auth_redirect')) {
            $url = $session->get('armd_social_auth.post_auth_redirect');
            $session->remove('armd_social_auth.post_auth_redirect');
        } else {
            $url = $this->get('router')->generate('armd_main_homepage');
        }
        return new RedirectResponse($url);
    }

}

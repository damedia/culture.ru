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
     * @Route("/vkontakte/auth", name="armd_social_auth_vkontakte_auth_dialog")
     */
    public function vkontakteAuthDialogAction()
    {
        $request = $this->getRequest();
        $host = $request->getHost();

        $authProvidersParams = $this->container->getParameter('armd_social_auth_auth_providers');
        if(empty($authProvidersParams[$host]['vkontakte'])) {
            throw new InvalidConfigurationException('Vkontakte authentication provider options for host ' . $host . ' was not found');
        }
        $authProvidersParams = $authProvidersParams[$host]['vkontakte'];

        $redirectUrl = $this->generateUrl('armd_social_auth_auth_result', array(
            'armd_social_auth_provider' => 'vkontakte'
        ), true);

        $loginFormUrl = 'http://oauth.vk.com/authorize?';
        $loginFormUrl .= 'client_id=' . $authProvidersParams['app_id'];
        $loginFormUrl .= '&scope=notify,offline';
        $loginFormUrl .= '&redirect_uri=' . urlencode($redirectUrl);
        $loginFormUrl .= '&response_type=code';

        $response = new RedirectResponse($loginFormUrl);
        return $response;
    }

    /**
     * @Route("/facebook/auth", name="armd_social_auth_facebook_auth_dialog")
     */
    public function facebookAuthDialogAction()
    {
        $request = $this->getRequest();
        $host = $request->getHost();

        $authProvidersParams = $this->container->getParameter('armd_social_auth_auth_providers');
        if(empty($authProvidersParams[$host]['facebook'])) {
            throw new InvalidConfigurationException('Facebook authentication provider options for host ' . $host . ' was not found');
        }
        $authProvidersParams = $authProvidersParams[$host]['facebook'];

        $redirectUrl = $this->generateUrl('armd_social_auth_auth_result', array(
            'armd_social_auth_provider' => 'facebook'
        ), true);

        $facebookState = substr(md5(rand(1, 1000) . microtime()), 0, 15);
        $request->getSession()->set('armd_social_auth.facebook_state', $facebookState);

        $loginFormUrl = 'https://www.facebook.com/dialog/oauth?';
        $loginFormUrl .= 'client_id=' . $authProvidersParams['app_id'];
        $loginFormUrl .= '&redirect_uri=' . urlencode($redirectUrl);
        $loginFormUrl .= '&scope=user_about_me,user_birthday,user_location,email';
        $loginFormUrl .= '&state=' . $facebookState;

        $response = new RedirectResponse($loginFormUrl);
        return $response;

    }

    /**
     * @Route("/twitter/auth", name="armd_social_auth_twitter_auth_dialog")
     */
    public function twitterAuthDialogAction()
    {
        return new Response('');
    }

    /**
     * @Route("/google/auth", name="armd_social_auth_google_auth_dialog")
     */
    public function googleAuthDialogAction()
    {
        return new Response('');
    }

    /**
     * @Route("/odnoklassniki/auth", name="armd_social_auth_odnoklassniki_auth_dialog")
     */
    public function odnoklassnikiAuthDialogAction()
    {
        return new Response('');
    }

    /**
     * @Route("/auth-result", name="armd_social_auth_auth_result")
     * @Template()
     */
    public function authResultAction()
    {
        $session = $this->getRequest()->getSession();
        if($session->has('armd_social_auth.post_auth_redirect')) {
            $response = new RedirectResponse($session->get('armd_social_auth.post_auth_redirect'));
            $session->remove('armd_social_auth.post_auth_redirect');
            return $response;
        } else {
            return array();
        }
    }

//     MYTODO: remove if no more needed
//    protected function curlRequest($url)
//    {
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
//        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
//        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // times out after 4s
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//        $content = curl_exec($ch); // run the whole process
//        if($content === false) {
//            throw new \Exception('CURL request error');
//        }
//        curl_close($ch);
//
//        return $content;
//    }
}

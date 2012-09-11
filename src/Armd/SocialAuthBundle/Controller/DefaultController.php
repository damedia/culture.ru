<?php

namespace Armd\SocialAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route("/vkontakte/auth", name="armd_social_auth_vkontakte_auth_dialog")
     */
    public function vkontakteAuthDialogAction()
    {
        $request = $this->getRequest();
        $host = $request->getHost();

        $authProvidersParams = $this->container->getParameter('armd_social_auth_auth_providers');
        if(empty($authProvidersParams[$host])) {
            throw new InvalidConfigurationException('Authentication provider options for host ' . $host . ' was not found');
        }
        $authProvidersParams = $authProvidersParams[$host];

        $redirectUrl = $this->generateUrl('armd_social_auth_auth_result', array(
//            'referer' => $request->headers->get('referer'),
            'armd_social_auth_provider' => 'vkontakte'
        ), true);

        $loginFormUrl = 'http://oauth.vk.com/authorize?';
        $loginFormUrl .= 'client_id=' . $authProvidersParams['vkontakte']['app_id'];
        $loginFormUrl .= '&scope=notify,offline';
        $loginFormUrl .= '&redirect_uri=' . urlencode($redirectUrl);
        $loginFormUrl .= '&response_type=code';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $loginFormUrl); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // times out after 4s
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $loginForm = curl_exec($ch); // run the whole process
        if($loginForm === false) {
            throw new \Exception('Can\'t load vkontakte form');
        }
        curl_close($ch);
        $response = new Response($loginForm);
        $response->headers->set('Content-Type', 'text/html; charset=windows-1251');
        return $response;
    }

    /**
     * @Route("/auth-result", name="armd_social_auth_auth_result")
     * @Template()
     */
    public function authResultAction()
    {
        return array();
    }
}

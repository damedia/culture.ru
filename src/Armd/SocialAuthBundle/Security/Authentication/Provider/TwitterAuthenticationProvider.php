<?php

namespace Armd\SocialAuthBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Armd\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Armd\SocialAuthBundle\Security\Authentication\Token\TwitterToken;
use Buzz\Browser;

class TwitterAuthenticationProvider extends AbstractSocialAuthenticationProvider
{

    /**
     * Attempts to authenticates a TokenInterface object.
     *
     * @param TokenInterface $token The TokenInterface instance to authenticate
     *
     * @return TokenInterface|Response An authenticated TokenInterface instance, never null
     *
     * @throws AuthenticationException if the authentication fails
     */
    public function authenticate(TokenInterface $token)
    {
        $this->obtainRequestToken($token);
    }


    /**
     * Twitter "Step 1: Obtaining a request token"
     *
     * @param \Armd\SocialAuthBundle\Security\Authentication\Token\TwitterToken $token
     */
    public function obtainRequestToken(TwitterToken $token)
    {
        $url = 'https://api.twitter.com/oauth/request_token';
        $params = array(
            'oauth_callback' => $this->router->generate('armd_social_auth_auth_result')
        );
        $result = $this->twitterHttpRequest($url, $params, $token);
    }


    /**
     * Checks whether this provider supports the given token.
     *
     * @param TokenInterface $token A TokenInterface instance
     *
     * @return Boolean true if the implementation supports the Token, false otherwise
     */
    function supports(TokenInterface $token)
    {
        return $token instanceof TwitterToken;
    }

    public function getProviderName()
    {
        return 'twitter';
    }

    protected function twitterHttpRequest($url, array $parameters, TwitterToken $token)
    {
        $providerParams = $this->paramsReader->getParameters($this->getProviderName());

        //--- SIGN
        // collect oauth params
        $oauthParts = array(
            'oauth_consumer_key' => $providerParams['oauth_consumer_key'],
            'oauth_nonce' => md5(microtime() . rand(0,1000000)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0'
        );
        if(!empty($token->oauthToken)) {
            $oauthParts['oauth_token'] = $token->oauthToken;
        }

        // prepare parameters
        $parametersToSign = array_merge($parameters, $oauthParts);
        $encodedParametersToSign = array();
        foreach($parametersToSign as $key => $val) {
            $encodedParametersToSign[rawurlencode($key)] = rawurlencode($val);
        }
        uksort($encodedParametersToSign, 'strcmp');

        // build parameters string
        $parametersString = '';
        foreach($encodedParametersToSign as $key => $val) {
            $parametersString .= $key . '=' . $val . '&';
        }
        $parametersString = substr($parametersString, 0, -1);

        // build oauth "signature base string"
        $baseString = 'POST&' . rawurlencode($url) . '&' . rawurlencode($parametersString);

        $signingKey = $providerParams['consumer_secret'] . '&';
        if(!empty($token->oauthTokenSecret)) {
            $signingKey .= '&' . $token->oauthTokenSecret;
        }

        // and finally generate the sign
        $sign = base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));

        //--- /SIGN


        //--- REQUEST
        $oauthParams['oauth_signature'] = $sign;
        $oauthHeader = 'OAuth ';
        foreach($oauthParams as $key => $value) {
            $oauthHeader .= rawurlencode($key) . '="' . rawurlencode($value) . '", ';
        }
        $oauthHeader = substr($oauthHeader, 0, -2);


        $client = new \Buzz\Client\Curl();
        $client->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $browser = new Browser($client);

        $result = $browser->post($url, array('Authorization: ' . $oauthHeader), http_build_query($parameters));
        //--- /REQUEST

        return $result;
    }



}
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
use Buzz\Message\Request as HttpRequest;
use Buzz\Message\Response as HttpResponse;
use Buzz\Message\RequestInterface as HttpRequestInterface;


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
//        $params = array(
//            'oauth_callback' => $this->router->generate('armd_social_auth_auth_result', array(), true)
//        );
        //$result = $this->twitterHttpRequest($url, array(), $token);


        $url = 'https://api.twitter.com/oauth/request_token';
        $oauthParts = array(
            'oauth_consumer_key' => $providerParams['oauth_consumer_key'],
            'oauth_nonce' => md5(microtime() . rand(0,1000000)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
            'oauth_callback' => $this->router->generate('armd_social_auth_auth_result', array(), true),
        );
        if(!empty($token->oauthToken)) {
            $oauthParts['oauth_token'] = $token->oauthToken;
        }

        $this->myTwitterHttpRequest($url, $oauthParts, true);
        $this->hwiTwitterHttpRequest($url, $oauthParts, true);

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
            'oauth_version' => '1.0',
            'oauth_callback' => $this->router->generate('armd_social_auth_auth_result', array(), true),
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
        \gFuncs::dbgWriteLogVar($encodedParametersToSign, false, '$encodedParametersToSign'); // DBG:

        // build parameters string
        $parametersString = '';
        foreach($encodedParametersToSign as $key => $val) {
            $parametersString .= $key . '=' . $val . '&';
        }
        $parametersString = substr($parametersString, 0, -1);
        \gFuncs::dbgWriteLogVar($parametersString, false, '$parametersString'); // DBG:

        // build oauth "signature base string"
        $baseString = 'POST&' . rawurlencode($url) . '&' . rawurlencode($parametersString);
        \gFuncs::dbgWriteLogVar($baseString, false, '$baseString'); // DBG:

        $signingKey = $providerParams['consumer_secret'];
        if(!empty($token->oauthTokenSecret)) {
            $signingKey .= '&' . $token->oauthTokenSecret;
        }
        \gFuncs::dbgWriteLogVar($signingKey, false, '$signingKey'); // DBG:

        // and finally generate the sign
        $sign = base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));

        //--- /SIGN


        //--- REQUEST
        $parametersToSign['oauth_signature'] = $sign;
        uksort($parametersToSign, 'strcmp');
        $oauthHeader = 'Authorization: OAuth ';
        foreach($parametersToSign as $key => $value) {
            $oauthHeader .= rawurlencode($key) . '="' . rawurlencode($value) . '", ';
        }
        $oauthHeader = substr($oauthHeader, 0, -2);
        \gFuncs::dbgWriteLogVar($oauthHeader, false, '$oauthHeader'); // DBG:

        $client = new \Buzz\Client\Curl();
        $client->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $browser = new Browser($client);

        $result = $browser->post($url, array($oauthHeader)); //, http_build_query($parameters)
        \gFuncs::dbgWriteLogVar($result, false, '$result'); // DBG:

        return $result;
    }


    protected function myTwitterHttpRequest($url, array $parameters, TwitterToken $token)
    {
        $providerParams = $this->paramsReader->getParameters($this->getProviderName());

        //--- SIGN
        // collect oauth params
        $oauthParts = array(
            'oauth_consumer_key' => $providerParams['oauth_consumer_key'],
            'oauth_nonce' => md5(microtime() . rand(0,1000000)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
            'oauth_callback' => $this->router->generate('armd_social_auth_auth_result', array(), true),
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
        \gFuncs::dbgWriteLogVar($encodedParametersToSign, false, '$encodedParametersToSign'); // DBG:

        // build parameters string
        $parametersString = '';
        foreach($encodedParametersToSign as $key => $val) {
            $parametersString .= $key . '=' . $val . '&';
        }
        $parametersString = substr($parametersString, 0, -1);
        \gFuncs::dbgWriteLogVar($parametersString, false, '$parametersString'); // DBG:

        // build oauth "signature base string"
        $baseString = 'POST&' . rawurlencode($url) . '&' . rawurlencode($parametersString);
        \gFuncs::dbgWriteLogVar($baseString, false, '$baseString'); // DBG:

        $signingKey = $providerParams['consumer_secret'];
        if(!empty($token->oauthTokenSecret)) {
            $signingKey .= '&' . $token->oauthTokenSecret;
        }
        \gFuncs::dbgWriteLogVar($signingKey, false, '$signingKey'); // DBG:

        // and finally generate the sign
        $sign = base64_encode(hash_hmac('sha1', $baseString, $signingKey, true));

        //--- /SIGN


        //--- REQUEST
        $parametersToSign['oauth_signature'] = $sign;
        uksort($parametersToSign, 'strcmp');
        $oauthHeader = 'Authorization: OAuth ';
        foreach($parametersToSign as $key => $value) {
            $oauthHeader .= rawurlencode($key) . '="' . rawurlencode($value) . '", ';
        }
        $oauthHeader = substr($oauthHeader, 0, -2);
        \gFuncs::dbgWriteLogVar($oauthHeader, false, '$oauthHeader'); // DBG:

        $client = new \Buzz\Client\Curl();
        $client->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $browser = new Browser($client);

        $result = $browser->post($url, array($oauthHeader)); //, http_build_query($parameters)
        \gFuncs::dbgWriteLogVar($result, false, '$result'); // DBG:
    }

    protected function hwiTwitterHttpRequest($url, array $parameters, TwitterToken $token)
    {
        $providerParams = $this->paramsReader->getParameters($this->getProviderName());

        $oauthParts = array(
            'oauth_consumer_key' => $providerParams['oauth_consumer_key'],
            'oauth_nonce' => md5(microtime() . rand(0,1000000)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
            'oauth_callback' => $this->router->generate('armd_social_auth_auth_result', array(), true),
        );
        if(!empty($token->oauthToken)) {
            $oauthParts['oauth_token'] = $token->oauthToken;
        }

        $oauthParts['oauth_signature'] = \Armd\SocialAuthBundle\Security\OAuthUtils::signRequest('POST', $url, $oauthParts, $providerParams['consumer_secret']);
        $authorization = 'Authorization: OAuth';

        foreach ($oauthParts as $key => $value) {
            $value = rawurlencode($value);
            $authorization .= ", $key=\"$value\"";
        }

        $headers[] = $authorization;

        $request  = new HttpRequest(HttpRequestInterface::METHOD_POST, $url);
        $response = new HttpResponse();

        $request->setHeaders($headers);
        //$request->setContent($content);

        $client = new \Buzz\Client\Curl();
        $client->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $browser = new Browser($client);
        $browser->send($request, $response);

        \gFuncs::dbgWriteLogVar($request, false, 'request'); // DBG:
        \gFuncs::dbgWriteLogVar($response, false, 'response'); // DBG:
    }



}
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
        if(empty($token->oauthVerifier)) {
            $this->obtainRequestToken($token);
            return $this->redirectUser($token);
        } else {
            $this->obtainAccessToken($token);
            $this->obtainUserData($token);

            $user = $this->loadUser($token);
            if (!$user) {
                $user = $this->createUser($token);
            }

            if ($user) {
                $token->setAuthenticated(true);
                $token->setUser($user);
                return $token;
            }

        }
    }


    /**
     * Twitter "Step 1: Obtaining a request token"
     *
     * @param \Armd\SocialAuthBundle\Security\Authentication\Token\TwitterToken $token
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     * @return void
     */
    public function obtainRequestToken(TwitterToken $token)
    {
        $url = 'https://api.twitter.com/oauth/request_token';
        $result = $this->twitterHttpRequest('POST', $url, array(), $token);

        parse_str($result, $parsedResult);

        if(empty($parsedResult['oauth_token'])
            || empty($parsedResult['oauth_token_secret'])
            || empty($parsedResult['oauth_callback_confirmed']))
        {
            throw new AuthenticationException('Cant get twitters oauth_token');
        }

        $token->oauthToken = $parsedResult['oauth_token'];
        $token->oauthTokenSecret = $parsedResult['oauth_token_secret'];
    }

    /**
     * Twitter "Step 2: Redirecting the user"
     * @param \Armd\SocialAuthBundle\Security\Authentication\Token\TwitterToken $token
     * @return \Armd\SocialAuthBundle\Security\Authentication\Token\TwitterToken
     */
    public function redirectUser(TwitterToken $token)
    {
        $url = 'https://api.twitter.com/oauth/authenticate?oauth_token=' . $token->oauthToken;
        $token->response = new RedirectResponse($url);
        return $token;
    }

    /**
     * Twitter "Step 3: Converting the request token to an access token"
     * @param \Armd\SocialAuthBundle\Security\Authentication\Token\TwitterToken $token
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationException
     * @return void
     */
    public function obtainAccessToken(TwitterToken $token)
    {
        $url = 'https://api.twitter.com/oauth/access_token';
        $params = array('oauth_verifier' => $token->oauthVerifier);
        $result = $this->twitterHttpRequest('POST',$url, $params, $token);
        parse_str($result, $parsedResult);
        if (empty($parsedResult['oauth_token'])
            || empty($parsedResult['oauth_token_secret'])
            || empty($parsedResult['user_id'])
            || empty($parsedResult['screen_name'])

        ) {
            throw new AuthenticationException('Cant get access token');
        }

        $token->oauthToken = $parsedResult['oauth_token'];
        $token->oauthTokenSecret = $parsedResult['oauth_token_secret'];
        $token->twitterUserId = $parsedResult['user_id'];
    }

    public function obtainUserData(TwitterToken $token)
    {
        $url = 'https://api.twitter.com/users/show.json';
        $result = $this->twitterHttpRequest('GET', $url, array('user_id' => $token->twitterUserId), $token);
        $parsedResult = json_decode($result, true);
        if(empty($parsedResult['name'])) {
            throw new AuthenticationException('Cant get twitter user data');
        }
        $token->twitterUserData = $parsedResult;
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

    protected function twitterHttpRequest($httpMethod, $url, array $parameters, TwitterToken $token)
    {
        $providerParams = $this->paramsReader->getParameters($this->getProviderName());
        $httpMethod = strtoupper($httpMethod);

        //--- SIGN
        // collect oauth params
        $oauthParts = array(
            'oauth_consumer_key' => $providerParams['oauth_consumer_key'],
            'oauth_nonce' => md5(microtime() . rand(0,1000000)),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
            'oauth_callback' => $this->router->generate(
                'armd_social_auth_auth_result',
                array('armd_social_auth_provider' => 'twitter'),
                true
            )
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
        $baseString = $httpMethod . '&' . rawurlencode($url) . '&' . rawurlencode($parametersString);

        $signingKey = $providerParams['consumer_secret'];
        $signingKey .= '&' . $token->oauthTokenSecret;

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

        $client = new \Buzz\Client\Curl();
        $client->setOption(CURLOPT_SSL_VERIFYPEER, false);
        $browser = new Browser($client);

        if($httpMethod === 'POST') {
            $result = $browser->post($url, array($oauthHeader), http_build_query($parameters));
        } elseif ($httpMethod === 'GET') {

            $urlWithParams = $url;
            if (strpos($url, '?') === false) {
                $urlWithParams .= '?';
            }
            foreach($parameters as $key => $val) {
                $urlWithParams .= rawurlencode($key) . '=' . rawurlencode($val) . '&';
            }
            $urlWithParams = substr($urlWithParams, 0, -1);
            $result = $browser->get($urlWithParams, array($oauthHeader));
        } else {
            throw new \InvalidArgumentException('Request method must be POST or GET');
        }

        if($result) {
            return $result->getContent();
        } else {
            throw new AuthenticationException('Error during querying twitter');
        }

        return $result;
    }

    public function loadUser(TwitterToken $token)
    {
        if(strlen(trim($token->twitterUserId)) === 0) {
            throw new AuthenticationException('Trying to load user by empty twitter uid');
        }
        $repo = $this->em->getRepository('ArmdUserBundle:User');

        $user = $repo->findOneByTwUid($token->twitterUserId);
        if ($user) {
            return $user;
        }

        return false;
    }

    public function createUser(TwitterToken $token)
    {
        $nameParts = explode(' ', $token->twitterUserData['name']);
        if(empty($nameParts[1])) {
            $nameParts[1] = '';
        }

        $user = new User();
        $user->setEmail($token->twitterUserId  . '@twitter.com');
        $user->setPlainPassword(substr(md5(rand(0, 10000) . microtime()), 0, 15));
        $user->setUsername('tw' . $token->twitterUserId);
        $user->setEnabled(true);
        $user->setSalt(rand(0, 100000));
        $user->setLocked(false);
        $user->setExpired(false);
        $user->setCredentialsExpired(false);
        $user->setTwUid($token->twitterUserId);
        $user->setFirstname($nameParts[0]);
        $user->setLastname($nameParts[1]);
        $user->setRoles(array('ROLE_USER'));

        $this->userManager->updateUser($user, true);

        return $user;
    }


}
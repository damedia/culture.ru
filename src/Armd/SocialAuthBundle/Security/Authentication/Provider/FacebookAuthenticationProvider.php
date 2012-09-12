<?php

namespace Armd\SocialAuthBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Armd\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\NonceExpiredException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Armd\SocialAuthBundle\Security\Authentication\Token\FacebookToken;

class FacebookAuthenticationProvider implements AuthenticationProviderInterface
{
    protected $router;
    protected $em;
    protected $paramsReader;

    public function __construct(Container $container)
    {
        $this->router = $container->get('router');
        $this->em = $container->get('doctrine')->getEntityManager();
        $this->container = $container;
        $this->paramsReader = $container->get('armd_social_auth.provider_parameters_reader');
    }

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        $this->checkFacebookState($token);
        $this->retrieveAccessToken($token);
        $this->retrieveUserData($token);

        $user = $this->loadUser($token);
        if (!$user) {
            $user = $this->createUser($token);
        }

        if ($user) {
            $token->setAuthenticated(true);
            $token->setUser($user);
            return $token;
        }

        return false;
    }

    public function checkFacebookState(FacebookToken $token)
    {
        $storedFacebookState = $this->container->get('request')->getSession()->get('armd_social_auth.facebook_state');
        if(empty($token->accessState) || $storedFacebookState !== $token->accessState) {
            throw new AuthenticationException('Error during validation of facebook "state" variable');
        }
    }

    public function retrieveAccessToken(FacebookToken $token)
    {
        $socialParams = $this->paramsReader->getParameters($this->getProviderName());

        $redirectUrl = $this->router->generate('armd_social_auth_auth_result', array(
            'armd_social_auth_provider' => $this->getProviderName()
        ), true);

        $tokenUrl = 'https://graph.facebook.com/oauth/access_token?';
        $tokenUrl .= 'client_id=' . $socialParams['app_id'];
        $tokenUrl .= '&redirect_uri=' . urlencode($redirectUrl);
        $tokenUrl .= '&client_secret=' . $socialParams['secret'];
        $tokenUrl .= '&code=' . $token->accessCode;

        $content = $this->curlRequest($tokenUrl);
        if ($content === false) {
            throw new AuthenticationException('Can\'t get facebook access token');
        }
        parse_str($content, $accessTokenData);
        if (empty($accessTokenData['access_token'])) {
            return false;
        }

        $token->accessToken = $accessTokenData['access_token'];
        $token->accessTokenExpires = $accessTokenData['expires'];
        $token->accessTokenDateTime = new \DateTime();

        return true;
    }

    public function retrieveUserData(FacebookToken $token)
    {
        if (empty($token->accessToken)) {
            throw new AuthenticationException('Trying to retrieve facebook user data without access token');
        }

        $apiUrl = 'https://graph.facebook.com/me?';
        $apiUrl .= '&access_token=' . $token->accessToken;

        $result = $this->curlRequest($apiUrl);
        if ($result === false) {
            throw new AuthenticationException('Can\'t get facebook user data');
        }

        $userData = json_decode($result, true);
        $token->facebookUserData = $userData;

    }

    public function loadUser(FacebookToken $token)
    {
        if(strlen(trim($token->facebookUserData['id'])) === 0) {
            throw new AuthenticationException('Trying to load user by empty facebook uid');
        }
        $repo = $this->em->getRepository('ArmdUserBundle:User');

        $user = $repo->findOneByFbUid($token->facebookUserData['id']);
        if ($user) {
            return $user;
        }

        return false;
    }

    public function createUser(FacebookToken $token)
    {
        $userManager = $this->container->get('fos_user.user_manager.default');

        $user = new User();
        $user->setEmail($token->facebookUserData['email']);
        $user->setPlainPassword(substr(md5(rand(0, 10000) . microtime()), 0, 15));
        $user->setUsername('fb' . $token->facebookUserData['id']);
        $user->setEnabled(true);
        $user->setSalt(rand(0, 100000));
        $user->setLocked(false);
        $user->setExpired(false);
        $user->setCredentialsExpired(false);
        $user->setFbUid($token->facebookUserData['id']);
        $user->setFirstname($token->facebookUserData['first_name']);
        $user->setLastname($token->facebookUserData['last_name']);
        $user->setRoles(array('ROLE_USER'));

        $userManager->updateUser($user, true);

        return $user;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof FacebookToken;
    }

    public function curlRequest($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // times out after 4s
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch); // run the whole process
        if ($result === false) {
            throw new \Exception('Curl error');
        }
        curl_close($ch);
        return $result;
    }

    public function getProviderName()
    {
        return 'facebook';
    }
}
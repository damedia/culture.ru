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
use Armd\SocialAuthBundle\Security\Authentication\Token\VkontakteToken;

class VkontakteAuthenticationProvider implements AuthenticationProviderInterface
{
    protected $router;
    protected $em;

    public function __construct(Container $container)
    {
        $this->router = $container->get('router');
        $this->em = $container->get('doctrine')->getEntityManager();
        $this->container = $container;
    }

    public function getParameters()
    {
        $request = $this->container->get('request');
        $host = $request->getHost();
        $socialParams = $this->container->getParameter('armd_social_auth_auth_providers');

        if(empty($socialParams[$host]['vkontakte'])) {
            throw new InvalidConfigurationException('armd_social_auth_auth_providers for host ' . $host . ' was not found');
        }
        $socialParams = $socialParams[$host]['vkontakte'];

        return $socialParams;
    }

    public function authenticate(TokenInterface $token)
    {
        if(!$this->supports($token)) {
            return null;
        }

        $this->retrieveAccessToken($token);
        $this->retrieveUserData($token);

        $user = $this->loadUser($token);
        if (!$user) {
            $user = $this->createUser($token);
        }

        if($user) {
            $token->setAuthenticated(true);
            $token->setUser($user);
            return $token;
        }

        return false;
    }

    public function retrieveAccessToken(VkontakteToken $token)
    {
        $socialParams = $this->getParameters();

        $redirectUrl = $this->router->generate('armd_social_auth_auth_result', array(
                    'armd_social_auth_provider' => 'vkontakte'
                ), true);

        $tokenUrl = 'https://oauth.vk.com/access_token?';
        $tokenUrl .= 'client_id='. $socialParams['app_id'];
        $tokenUrl .= '&client_secret=' . $socialParams['secret'];
        $tokenUrl .= '&code=' . $token->accessCode;
        $tokenUrl .= '&redirect_uri=' . urlencode($redirectUrl);

        $content = $this->curlRequest($tokenUrl);
        if($content === false) {
            throw new AuthenticationException('Can\'t get vkontakte access token');
        }
        $content = json_decode($content, true);
        if(!empty($content['error'])) {
           return false;
        }

        $token->accessToken = $content['access_token'];
        $token->accessTokenExpiresIn = $content['expires_in'];
        $token->accessTokenDateTime = new \DateTime();
        $token->accessTokenUserId = $content['user_id'];

        return true;
    }

    public function retrieveUserData(VkontakteToken $token)
    {
        if(empty($token->accessTokenUserId)) {
            throw new AuthenticationException('Trying to retrieve vk user data with empty user_id');
        }
        if(empty($token->accessToken)) {
            throw new AuthenticationException('Trying to retrieve vk user data without access token');
        }

        $apiUrl = 'https://api.vk.com/method/users.get?';
        $apiUrl .= 'uids=' . $token->accessTokenUserId;
        $apiUrl .= '&access_token=' . $token->accessToken;
        $apiUrl .= '&fields=uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,has_mobile,rate,contacts,education,online,counters';

        $result = $this->curlRequest($apiUrl);
        if($result === false) {
            throw new AuthenticationException('Can\'t get vk user data');
        }

        $userData = json_decode($result, true);
        $token->vkUserData = $userData['response'][0];

    }

    public function loadUser(VkontakteToken $token)
    {
        $repo = $this->em->getRepository('ArmdUserBundle:User');

        $user = $repo->findOneByVkUid($token->accessTokenUserId);
        if($user) {
           return $user;
        }

        return false;
    }

    public function createUser(VkontakteToken $token)
    {
        $userManager = $this->container->get('fos_user.user_manager.default');

        $user = new User();
        $user->setEmail($token->vkUserData['uid'] . '@vk.com');
        $user->setPlainPassword(substr(md5(rand(0, 10000) . microtime()), 0, 15));
        $user->setUsername('vk' . $token->vkUserData['uid']);
        $user->setEnabled(true);
        $user->setSalt(rand(0, 100000));
        $user->setLocked(false);
        $user->setExpired(false);
        $user->setCredentialsExpired(false);
        $user->setVkUid($token->vkUserData['uid']);
        $user->setFirstname($token->vkUserData['first_name']);
        $user->setLastname($token->vkUserData['last_name']);
        $user->setRoles(array('ROLE_USER'));

        $userManager->updateUser($user, true);

        return $user;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof VkontakteToken;
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
        if($result === false) {
            var_dump(curl_error($ch));
            throw new \Exception('Curl error');
        }
        curl_close($ch);
        return $result;
    }
}
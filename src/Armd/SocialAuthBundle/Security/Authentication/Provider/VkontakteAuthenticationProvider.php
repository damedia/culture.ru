<?php

namespace Armd\SocialAuthBundle\Security\Authentication\Provider;

use Armd\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Armd\SocialAuthBundle\Security\Authentication\Token\VkontakteToken;

class VkontakteAuthenticationProvider extends AbstractSocialAuthenticationProvider
{

    public function authenticate(TokenInterface $token)
    {
        if (!$this->supports($token)) {
            return null;
        }

        /** @var $token VkontakteToken */
        if(empty($token->accessCode)) {
            return $this->redirectLoginForm($token);
        }

        $this->retrieveAccessToken($token);
        $this->retrieveUserData($token);

        $user = $this->loadUser($token);
        if (!$user) {
            $user = $this->createUser($token);
        }

        if ($user) {
            $token->setUser($user);
            $token->setAuthenticated(true);
        }
        else {
            $token->setAuthenticated(false);
        }

        return $token;
    }

    public function redirectLoginForm(VkontakteToken $token)
    {
        $providerParams = $this->paramsReader->getParameters($this->getProviderName());
        $redirectUrl = $this->router->generate('armd_social_auth_auth_result', array(
            'armd_social_auth_provider' => 'vkontakte'
        ), true);

        $loginFormUrl = 'http://oauth.vk.com/authorize?';
        $loginFormUrl .= 'client_id=' . $providerParams['app_id'];
        $loginFormUrl .= '&scope=notify,offline';
        $loginFormUrl .= '&redirect_uri=' . urlencode($redirectUrl);
        $loginFormUrl .= '&response_type=code';

        $token->response = new RedirectResponse($loginFormUrl);
        return $token;
    }


    public function retrieveAccessToken(VkontakteToken $token)
    {
        $socialParams = $this->paramsReader->getParameters($this->getProviderName());

        $redirectUrl = $this->router->generate('armd_social_auth_auth_result', array(
            'armd_social_auth_provider' => $this->getProviderName()
        ), true);

        $tokenUrl = 'https://oauth.vk.com/access_token?';
        $tokenUrl .= 'client_id=' . $socialParams['app_id'];
        $tokenUrl .= '&client_secret=' . $socialParams['secret'];
        $tokenUrl .= '&code=' . $token->accessCode;
        $tokenUrl .= '&redirect_uri=' . urlencode($redirectUrl);

        $content = $this->curlRequest($tokenUrl);
        if ($content === false) {
            throw new AuthenticationException('Can\'t get vkontakte access token');
        }
        $content = json_decode($content, true);
        if (!empty($content['error'])) {
            throw new AuthenticationException('Can\'t get vkontakte access token');
        }

        $token->accessToken = $content['access_token'];
        $token->accessTokenExpiresIn = $content['expires_in'];
        $token->accessTokenDateTime = new \DateTime();
        $token->accessTokenUserId = $content['user_id'];

        return true;
    }

    public function retrieveUserData(VkontakteToken $token)
    {
        if (empty($token->accessTokenUserId)) {
            throw new AuthenticationException('Trying to retrieve vk user data with empty user_id');
        }
        if (empty($token->accessToken)) {
            throw new AuthenticationException('Trying to retrieve vk user data without access token');
        }

        $apiUrl = 'https://api.vk.com/method/users.get?';
        $apiUrl .= 'uids=' . $token->accessTokenUserId;
        $apiUrl .= '&access_token=' . $token->accessToken;
        $apiUrl .= '&fields=uid,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,photo,photo_medium,photo_big,has_mobile,rate,contacts,education,online,counters';

        $result = $this->curlRequest($apiUrl);
        if ($result === false) {
            throw new AuthenticationException('Can\'t get vk user data');
        }

        $userData = json_decode($result, true);
        $token->vkUserData = $userData['response'][0];

    }

    public function loadUser(VkontakteToken $token)
    {
        if (strlen(trim($token->accessTokenUserId)) === 0) {
            throw new AuthenticationException('Trying to load user by empty vkontakte uid');
        }
        $repo = $this->em->getRepository('ArmdUserBundle:User');

        $user = $repo->findOneByVkontakteUid($token->accessTokenUserId);
        return $user;
    }

    public function createUser(VkontakteToken $token)
    {
        $user = new User();
        $user->setEmail($token->vkUserData['uid'] . '@vk.com');
        $user->setPlainPassword(substr(md5(rand(0, 10000) . microtime()), 0, 15));
        $user->setUsername('vk' . $token->vkUserData['uid']);
        $user->setEnabled(true);
        $user->setSalt(rand(0, 100000));
        $user->setLocked(false);
        $user->setExpired(false);
        $user->setCredentialsExpired(false);
        $user->setVkontakteUid($token->vkUserData['uid']);
        $user->setFirstname($token->vkUserData['first_name']);
        $user->setLastname($token->vkUserData['last_name']);
        $user->setRoles(array('ROLE_USER'));

        $this->userManager->updateUser($user, true);

        return $user;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof VkontakteToken;
    }


    public function getProviderName()
    {
        return 'vkontakte';
    }
}
<?php

namespace Zim32\LoginzaBundle\DependencyInjection\Security;

use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Zim32\LoginzaBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\DependencyInjection\Container;

class LoginzaListener implements ListenerInterface  {

    protected $securityContext;
    protected $authenticationManager;
    protected $container;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, Container $container)
    {
        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->container = $container;
    }

    public function handle(GetResponseEvent $event){
        $request = $event->getRequest();
        if($request->request->has('token') !== false){
            $loginzaToken = $request->request->get('token');
            $signature = md5($loginzaToken.$this->container->getParameter('security.loginza.secret_key'));
            $url = "http://loginza.ru/api/authinfo?token={$loginzaToken}&id={$this->container->getParameter('security.loginza.widget_id')}&sig={$signature}";
            $ch = curl_init($url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            $decoded = json_decode($result,true);

            if(empty($decoded)) throw new AuthenticationException("Wrong loginza response format");
            if(isset($decoded['error_message'])) throw new AuthenticationException($decoded['error_message']);

            $userParam = $this->getUserParams($decoded);
            $session = $this->container->get('session');
            $session->set('loginza_data', $userParam);

            $user = false;
            if($repo = $this->container->getParameter('security.loginza.entity')){
                $user = $this->loadUserFromDoctrine($decoded, $repo);
            }

            if($user) {
                $token = new LoginzaToken($user->getRoles());
                $token->setUser($user);
                $token->setAuthenticated(true);
                $token->setAttribute('loginza_info', $decoded);
                $this->container->get('security.context')->setToken($token);
            }
        }
    }

    protected function loadUserFromDoctrine($data, $repository){
        $em = $this->container->get('doctrine')->getEntityManager();
        try{
            $repo = $em->getRepository($repository);
        }catch(\Exception $e){
            throw $e;
            return null;
        }

        try{
            $userParam = $this->getUserParams($data);
            $user = $repo->findOneBy(array($userParam['provider'].'Uid'=>$data['uid']));
        }catch(\Exception $e){
            throw $e;
            return null;
        }

        return $user;
        
    }

    protected function getUserParams( $decoded ) {
        if(isset($decoded['provider'])) {
            $userEmail = isset($decoded['email']) ? $decoded['email'] : '';
            if($decoded['provider'] == 'http://odnoklassniki.ru/') {
                $userProvider = 'ok';
                $userName = isset($decoded['name']['full_name']) ? $decoded['name']['full_name'] : '';
            } elseif($decoded['provider'] == 'http://vkontakte.ru/') {
                $userProvider = 'vk';
                $userName = '';
                if(isset($decoded['name']['first_name'])) {
                    $userName .= $decoded['name']['first_name'];
                }
                if(isset($decoded['name']['last_name'])) {
                    if($userName != '') {
                        $userName .= ' ';
                    }
                    $userName .= $decoded['name']['last_name'];
                }
            } elseif($decoded['provider'] == 'http://www.facebook.com/') {
                $userProvider = 'fb';
                $userName = isset($decoded['name']['full_name']) ? $decoded['name']['full_name'] : '';
            } elseif($decoded['provider'] == 'http://twitter.com/') {
                $userProvider = 'tw';
                $userName = isset($decoded['name']['full_name']) ? $decoded['name']['full_name'] : '';
            } else {
                throw new AuthenticationException("No provider set");
            }
        } else {
            throw new AuthenticationException("Unsupportet provider");
        }

        return array(
            'uid' => $decoded['uid'],
            'provider' => $userProvider,
            'name' => $userName,
            'email' => $userEmail,
        );
    }
}

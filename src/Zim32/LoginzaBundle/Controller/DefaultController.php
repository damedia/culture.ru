<?php

namespace Zim32\LoginzaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Zim32\LoginzaBundle\DependencyInjection\Security\LoginzaToken;
use Symfony\Component\Security\Core\SecurityContextInterface;

class DefaultController extends Controller
{
    
    public function indexAction()
    {
        //new \Symfony\Component\HttpFoundation\Session\Session();
        $name = $this->get('security.context')->getToken()->getUsername();
        return $this->render('Zim32LoginzaBundle:Default:index.html.twig', array('name' => $name));
    }

    public function loginAction() {
        $token_url = $this->generateUrl($this->container->getParameter('security.loginza.token_route'), array(), true);
        return $this->render('Zim32LoginzaBundle:UserProfile:login.html.twig', array('token_url'=>$token_url));
    }

    public function socialRegisterAction(Request $request) {
        $token = $this->container->get('security.context')->getToken();
        if(!$token instanceof \Symfony\Component\Security\Core\Authentication\Token\AnonymousToken) {
            return $this->redirect(
                $this->generateUrl("cp")
            );
        }

        $session = $this->container->get('session');
        //$session = new \Symfony\Component\HttpFoundation\Session\Session();
        $loginzaData = $session->get('loginza_data');

        //$user = new \Application\Sonata\UserBundle\Entity\User();
        //$user = new \Application\Sonata\UserBundle\Entity\User();
        $user = new \Zim32\LoginzaBundle\Entity\User();
        $user->setSocialName($loginzaData['name']);
        $user->setEmail($loginzaData['email']);

        $form = $this->createFormBuilder( $user )
            ->add('email', 'text')
            ->add('userName', 'text')
            ->getForm();

        if ($request->getMethod() == 'POST' && false) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $user->setRoles(array('ROLE_LOGINZA'));
                $provideUidSetMethod = 'set'.ucfirst($loginzaData['provider']).'Uid';
                $user->$provideUidSetMethod($loginzaData['uid']);
                $user->setPassword($loginzaData['uid']);
                $user->setSocialName($loginzaData['name']);

                $em = $this->container->get('doctrine')->getEntityManager();
                $em->persist($user);
                $em->flush();

                $token = new LoginzaToken($user->getRoles());
                $token->setUser($user);
                $token->setAuthenticated(true);
                //$token->setAttribute('loginza_info', $decoded);
                $this->container->get('security.context')->setToken($token);
                return $this->redirect(
                    $this->generateUrl("_loginza_redirect_user")
                );
            }
        }


        return $this->render('Zim32LoginzaBundle:UserProfile:email.html.twig', array('form' => $form->createView()));
    }

    public function socialSaveAction(Request $request) {
        $session = $this->container->get('session');
        $loginzaData = $session->get('loginza_data');

        //$user = new \Application\Sonata\UserBundle\Entity\User();
        $user = new \Zim32\LoginzaBundle\Entity\User();

        $form = $this->createFormBuilder( $user )
            ->add('email', 'text')
            ->add('userName', 'text')
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if ($form->isValid()) {
                $user->setRoles(array('ROLE_USER'));
                $provideUidSetMethod = 'set'.ucfirst($loginzaData['provider']).'Uid';
                $user->$provideUidSetMethod($loginzaData['uid']);
                $user->setPassword($loginzaData['uid']);
                $user->setSocialName($loginzaData['name']);
                $user->setEnabled( true );

                $em = $this->container->get('doctrine')->getEntityManager();
                $em->persist($user);
                $em->flush();

                $token = new LoginzaToken($user->getRoles());
                $token->setUser($user);
                $token->setAuthenticated(true);
                $token->setAttribute('loginza_info', $loginzaData);
                //$token->setAttribute('loginza_info', $decoded);
                //$this->container->get('session')->set('_security_main', serialize($token));

                //echo '<pre>'; //var_dump($token);
                //var_dump($token->getUser()); die();
                $this->container->get('security.context')->setToken($token);
                //echo '<pre>'; var_dump($this->container->get('security.context')); //die();

                //new \Symfony\Component\Security\Core\SecurityContext

                //return new \Symfony\Component\HttpFoundation\Response('Ok', 200);
                return $this->redirect(
                    $this->generateUrl("cp")
                );
            }
        }
    }
}

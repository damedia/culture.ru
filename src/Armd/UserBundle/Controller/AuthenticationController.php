<?php

namespace Armd\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Armd\UserBundle\Entity\User;


class AuthenticationController extends Controller
{
    /**
     * @Route("/info", name="armd_user_info")     
     */
    function userAction()
    {
        $id = $this->getRequest()->get('id');
        $hash = $this->getRequest()->get('hash');

        $user = $this->getDoctrine()->getRepository('ArmdUserBundle:User')->createQueryBuilder('u')
            ->andWhere('u.id = :id')
            ->andWhere('u.password = :password')
            ->setParameter('id', $id)
            ->setParameter('password', $hash)
            ->getQuery()
            ->getArrayResult();
            
        $response = new Response(json_encode($user));    
        $response->headers->set('Content-Type', 'application/json');    
        
        return $response; 
    }

    function authenticateByLoginTokenAction($loginToken)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('ArmdUserBundle:User')
            ->createQueryBuilder('u')
            ->where('u.loginToken = :login_token')
            ->andWhere('u.loginTokenExpires > :now')
            ->setParameter('login_token',  $loginToken)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();

        if(count($users) !== 1) {
            throw new \LogicException('Could not find user by login token');
        }

        /** @var $user User */
        $user = $users[0];
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.context')->setToken($token);

        $user->setLoginToken(null);
        $user->setLoginTokenExpires(null);
        $em->persist($user);
        $em->flush();

        if($this->getRequest()->get('return_url')) {
            $returnUrl = $this->getRequest()->get('return_url');
        } elseif($this->getRequest()->headers->has('referer')) {
            $returnUrl = $this->getRequest()->headers->get('referer');
        } else {
            $returnUrl = '/';
        }

        $response = new RedirectResponse($returnUrl);

        return $response;
    }
}

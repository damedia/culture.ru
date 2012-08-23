<?php

namespace Armd\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


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
}

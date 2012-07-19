<?php

namespace Zim32\LoginzaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction()
    {
        $name = $this->get('security.context')->getToken()->getUsername();
        return $this->render('Zim32LoginzaBundle:Default:index.html.twig', array('name' => $name));
    }

    public function loginAction(){
        $token_url = $this->generateUrl($this->container->getParameter('security.loginza.token_route'), array(), true);
        return $this->render('Zim32LoginzaBundle:UserProfile:login.html.twig', array('token_url'=>$token_url));
    }
}

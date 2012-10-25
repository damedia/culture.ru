<?php

namespace Armd\AtlasBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use FOS\RestBundle\Controller\Annotations as Rest;

class AuthController extends Controller
{
    /**
     * @Route("/auth/login")
     * @Method({"GET","POST"})
     */
    public function loginAction()
    {
        $login = '';
        $password = '';

        $id = $this->getRequest()->get('id');
        $hash = $this->getRequest()->get('hash');

        $user = $this->getDoctrine()->getRepository('ArmdUserBundle:User')->createQueryBuilder('u')
            ->andWhere('u.id = :id')
            ->andWhere('u.password = :password')
            ->setParameter('id', $id)
            ->setParameter('password', $hash)
            ->getQuery()
            ->getArrayResult();


        if ($this->getRequest()->isMethod('POST')) {
            $login = $this->getRequest()->query->get('login');
            $password = $this->getRequest()->get('password');
        }

        $sessionId = '';

        $result = array(
            'sessionId' => $sessionId,
            'login' => $login,
        );
        return new Response(json_encode(array(
            'success' => true,
            'message' => 'OK',
            'result' => $result,
        )));
    }
}
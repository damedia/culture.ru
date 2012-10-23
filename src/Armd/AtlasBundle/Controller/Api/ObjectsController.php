<?php

namespace Armd\AtlasBundle\Controller\Api;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
//use FOS\RestBundle\Controller\Annotations\RouteResource;

class ObjectsController
{
    /**
     * @Route("/objects")
     * @Rest\View
     */
    public function indexAction()
    {
        $users = array('id' => microtime());
        return array('users' => $users);
    }
}
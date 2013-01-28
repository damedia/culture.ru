<?php
namespace Armd\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    public function testCacheAction()
    {
        $response = new Response(rand(0, 1000));
        $response->setSharedMaxAge(0);

        return $response;
    }
}
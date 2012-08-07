<?php

namespace Armd\TvigleBundle\Controller;

use Armd\TvigleBundle\Entity\Tvigle;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CallbackController extends Controller
{
    public function updateAction( $id )
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $tvigle = $em->getRepository('Armd\TvigleBundle\Entity\Tvigle')->findOneById( $id );

        $status = $this->container->get('request')->get('st');
        if(6 == $status) {
            $params = $this->container->getParameter('tvigle');
            $serviceUrl = $params['api_service_url'];
            $Login = $params['api_login'];
            $Password = $params['api_password'];
            $soap = new \SoapClient
            (
                $serviceUrl,
                array
                (
                    'login'     =>    $Login,
                    'password'  =>    $Password
                )
            );
            $videoItem = $soap->VideoItem( $tvigle->getTvigleId() );


            $code = $soap->GetEmbedId($tvigle->getTvigleId(), 442, 1, 40);

            $tvigle->setDuration( $videoItem->duration );
            $tvigle->setImage( $videoItem->img );
            $tvigle->setCode( $code );
            $tvigle->setStatus( $status );

            $em->persist($tvigle);
            $em->flush();
        } else {
            $tvigle->setStatus( $status );
            $em->persist($tvigle);
            $em->flush();
        }

        return new Response('Ok', 200);
    }

}

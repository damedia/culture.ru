<?php

namespace Armd\TvigleBundle\Controller;

use Armd\TvigleBundle\Entity\Tvigle;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class TvigleController extends Controller
{
    public function getParamsValue($name, $default = null)
    {
        $value = $this->params->getParam($name)->getValue();

        return $value ? $value : $default;
    }

    /**
     * Lists all News entities.
     *
     */
    public function listAction()
    {
        $repository = $this->getDoctrine()->getEntityManager()->getRepository('ArmdTvigleBundle:Tvigle');
        $entities = $repository->findBy(array('status' => 6));

        return $this->render('ArmdTvigleBundle:Default:list.tvigle.html.twig', array(
            'entities'     => $entities
        ));
    }

    /**
     * Finds and displays a News entity.
     *
     */
    public function itemAction($id)
    {
        $repository = $this->getDoctrine()->getEntityManager()->getRepository('ArmdTvigleBundle:Tvigle');
        $entity = $repository->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity');
        }

        return $this->render('ArmdTvigleBundle:Default:item.tvigle.html.twig', array(
            'entity'     => $entity
        ));
    }

    public function getCodeAction( $id )
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $tvigle = $em->getRepository('Armd\TvigleBundle\Entity\Tvigle')->findOneById( $id );

        $code = $tvigle->getCode();

        $data = array(
            'code' => $code,
        );

        $contentAsJson = json_encode($data);
        return new Response(
            $contentAsJson,
            $status = 200,
            array('Content-Type' => 'application/json')
        );
    }
}

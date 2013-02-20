<?php

namespace Armd\OnlineTranslationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{ 	
    public function testIframeAction($id)
    {
        $entity = $this->getDoctrine()->getRepository('ArmdOnlineTranslationBundle:OnlineTranslation')->find($id);
        
        if ($entity === null) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $id));
        }
        
        return new Response($entity->getDataCode());
    }
    
    public function homepageAction()
    {
        $entities = $this->getDoctrine()
            ->getRepository('ArmdOnlineTranslationBundle:OnlineTranslation')
            ->findBy(array('published' => 1), array('date' => 'DESC'), 1);
        
        if (isset($entities[0])) {
            $entity = $entities[0];
            setlocale(LC_TIME, 'ru_RU');
            $date = strftime('%e %B, %A', $entity->getDate()->getTimestamp());
        } else {
            $entity = null;
        }
        
        return $this->render('ArmdOnlineTranslationBundle:Default:homepage.html.twig',
            array(
                'entity' => $entity,
                'date' => $date
            )
        );
    }
}

<?php

namespace Armd\ListBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ListController extends BaseController
{
//    /**
//     * @param integer $page
//     * @Route("/", defaults={"page" = 1})
//     * @Route("/page/{page}", requirements={"page" = "\d+"})
//     */
//    function listAction($page, $limit = 10)
//    {
//        return $this->render($this->getTemplateName('list'), array('entities' => $this->getPagination($this->getListRepository()->getQuery(), $page, $limit)));
//    }

    /**
     * @param integer $id
     * @Route("/{id}", requirements={"id" = "\d+"})     
     */
    function itemAction($id)
    {
        $entity = $this->getEntityRepository()->find($id);

        if (null === $entity) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $id));
        }

        return $this->render($this->getTemplateName('item'), array('entity' => $entity));
    }

    /**
     * @return Armd\ListBundle\Repository\ListRepository
     */
    function getListRepository()
    {
        $repository = $this->getEntityRepository();

        return $repository;
    }
    
    /**
     * @return string
     */            
    function getControllerName()
    {
        return 'ArmdListBundle:List';
    }    
}

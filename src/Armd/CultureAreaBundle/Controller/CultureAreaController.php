<?php

namespace Armd\CultureAreaBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\Bundle\CmsBundle\Controller\Controller;

class CultureAreaController extends Controller
{           
    public function listAction(UsageType $params)
    {   
        $entities = $this->getEntityRepository($params)->findAll();        

        return $this->renderCms($params, array( 'entities' => $entities ));
    }

    public function itemAction(UsageType $params, $id)
    {
        $entity = $this->getEntityRepository($params)->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }

        return $this->renderCms($params, array( 'entity' => $entity ));
    }
}

<?php

namespace Armd\CommonBundle\Controller;

use Armd\Bundle\CmsBundle\Controller\Controller;

class BaseController extends Controller
{
    /**
     * @param integer $id
     * @return mixed
     */    
    public function itemAction($id)
    {
        $entity = $this->getEntityRepository()->find($id);
            
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find entity');
        }

        return $this->renderCms(array('entity' => $entity));        
    }
}

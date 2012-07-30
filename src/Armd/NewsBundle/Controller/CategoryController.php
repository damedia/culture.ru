<?php

namespace Armd\NewsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Armd\ListBundle\Controller\BaseController;

use Armd\NewsBundle\Calendar\Calendar;

class CategoryController extends BaseController
{
    function listAction()
    {
        return $this->render($this->getTemplateName('list'), 
            array('entities' => $this->getEntityRepository()->findBy(array('filtrable' => '1'), array('priority' => 'ASC')))
        );
    }
    
    function getControllerName()
    {
        return 'ArmdNewsBundle:Category';
    }    
}

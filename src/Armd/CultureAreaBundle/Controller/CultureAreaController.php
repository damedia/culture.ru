<?php

namespace Armd\CultureAreaBundle\Controller;

use Armd\Bundle\CmsBundle\Controller\Controller;
use Armd\Bundle\CmsBundle\UsageType\UsageType;

class CultureAreaController extends Controller
{
    /**
     * @param Armd\Bundle\CmsBundle\UsageType\UsageType $params
     * @param $name
     * @return mixed
     */
    public function indexAction()
    {
        return $this->renderCms(array(
            'message' => 'Simple bundle'
        ));
    }
}

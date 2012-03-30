<?php

namespace Armd\CommonBundle\Controller;

use Armd\Bundle\CmsBundle\Controller\Controller;
use Armd\Bundle\CmsBundle\UsageType\UsageType;

class CommonController extends Controller
{
    /**
     * @param Armd\Bundle\CmsBundle\UsageType\UsageType $params
     * @param $name
     * @return mixed
     */
    public function indexAction(UsageType $params)
    {
        return $this->renderCms($params, array(
            'message' => 'Simple bundle'
        ));
    }
}

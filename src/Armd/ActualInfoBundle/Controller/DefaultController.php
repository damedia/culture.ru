<?php

namespace Armd\ActualInfoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Template()
     * @return array
     */
    public function mainpageWidgetAction()
    {
        $actualInfo = $this->getDoctrine()->getRepository('ActualInfoBundle:ActualInfo')->findForMainPage();
        return array('actualInfo' => $actualInfo);
    }
}

<?php

namespace Armd\ActualInfoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @param $date
     * @Template()
     * @return array
     */
    public function mainpageWidgetAction($date = '')
    {
        /** @var \Armd\ActualInfoBundle\Entity\ActualInfoRepository $repo */
        $repo = $this->getDoctrine()->getRepository('ActualInfoBundle:ActualInfo');
        $actualInfo = $repo->findForMainPage($date);

        return array('actualInfo' => $actualInfo);
    }
}

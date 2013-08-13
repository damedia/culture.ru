<?php

namespace Armd\PressCenterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Template()
     * @return array
     */
    public function indexAction()
    {
        $repo = $this->getPressCenterRepository();
        return  array('items' => $repo->findAll());
    }

    /**
     * @Template()
     * @param $slug
     * @return array|\Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function itemAction($slug)
    {
        $repo = $this->getPressCenterRepository();
        $item = $repo->findOneBy(array('slug' => $slug));
        if (!$item) {
            return $this->createNotFoundException('Press center material is not found with slug "' . $slug . '"');
        }
        return  array('item' => $item);
    }


    /**
     * @param $date
     * @Template()
     * @return array
     */
    public function mainpageWidgetAction($date = '')
    {
        $repo = $this->getPressCenterRepository();
        $items = $repo->findForMainPage($date, 5);

        return array('items' => $items);
    }

    /**
     * @return \Armd\PressCenterBundle\Entity\PressCenterRepository
     */
    private function getPressCenterRepository()
    {
        return $this->getDoctrine()->getRepository('PressCenterBundle:PressCenter');
    }
}

<?php

namespace Armd\MainBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Symfony\Component\HttpFoundation\Request;

class MainAdminController extends BaseController
{
    public function listAction()
    {
        $request = $this->getRequest();
        if ($request->isMethod('POST')) {
            return $this->redirect(
                $this->generateUrl(
                    'armd_main_homepage_preview',
                    array('date' => $request->get('year') . '-' . $request->get('month') . '-' . $request->get('day'))
                )
            );
        }

        return $this->render(
            'ArmdMainBundle:Admin:list.html.twig',
            array(
                'day' => date('d'),
                'month' => date('m'),
                'year' => date('Y'),
            )
        );
    }

}
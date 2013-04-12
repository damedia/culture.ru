<?php

namespace Armd\MarkupBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MarkupController extends Controller
{
    /**
     * @Route("/view/{template}")
     * @Template()
     */
    public function viewAction($name)
    {
        return array('name' => $name);
    }
}

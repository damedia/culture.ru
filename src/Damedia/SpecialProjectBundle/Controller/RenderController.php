<?php

namespace Damedia\SpecialProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RenderController extends Controller {
    public function indexAction($page) {
        return $this->render('DamediaSpecialProjectBundle:Default:default.html.twig', array("parameter" => $page));
    }
}

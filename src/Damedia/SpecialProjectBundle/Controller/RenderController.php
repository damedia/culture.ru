<?php
namespace Damedia\SpecialProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RenderController extends Controller {
    public function indexAction($page) {
        switch ($page) {
            case "tolstoy":
                $template = "tolstoy.html.twig";
                break;

            default:
                $template = "default.html.twig";
        }


        return $this->render('DamediaSpecialProjectBundle:Default:'.$template);
    }
}

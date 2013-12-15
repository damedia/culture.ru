<?php
namespace Damedia\TinymceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RenderController extends Controller {
    public function formAction() {
        $neighborsCommunicator = $this->get('special_project_neighbors_communicator');
        $entitySelectOptions = $neighborsCommunicator->getFriendlyEntitiesSelectOptions();

        return $this->render('DamediaTinymceBundle:Default:mainForm.html.twig', array('entitySelectOptions' => $entitySelectOptions));
    }
}
?>
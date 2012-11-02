<?php

namespace Armd\MuseumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="armd_museum_index")
     * @Template()
     */
    public function indexAction()
    {
        return array(
        );
    }

    /**
     * @Route("/museums-list", name="armd_museum_list", options={"expose"=true})
     * @Template()
     */
    public function listAction($page = 1, $perPage = 1000)
    {
        $criteria = array(
            'search_string' => $this->getRequest()->get('searchString')
        );
        return array(
            'museums' => $this->get('armd_museum.manager.museum')->getPager($criteria, $page, $perPage),
        );
    }
}

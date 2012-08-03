<?php

namespace Armd\ChronicleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Armd\ListBundle\Controller\ListController;
use Armd\ChronicleBundle\Util\RomanConverter as Converter;

class EventController extends ListController
{
    /**
     * @Route("/", name="armd_chronicle_index", defaults={"century" = "XXI"})     
     * @Route("/{century}/", requirements={"century" = "[IVXLCDM]+"}, name="armd_chronicle_index_by_century")     
     */
    public function indexAction($century)
    {
        var_dump($this->getCenturiesList($century));
        return $this->render($this->getTemplateName('chronicle'), array(
            'century'   => $century,
        ));
    }
    
    function getDatePeriod($century)
    {
        $year = Converter::toNumber($century) * 100;
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', "{$year}-01-01 00:00:00");
        var_dump($date);
    }
    
    function centuriesAction($century)
    {
        return $this->render($this->getTemplateName('centuries'), array(
            'centuries'    => $this->getCenturiesList($century),
        ));
    }
    
    function getCenturiesList($century)
    {
        $result = array();
        $year = $this->getCenturiesRepository()->getQuery()->getArrayResult();
    }
    
    function getCenturiesRepository()
    {
        return $this->getListRepository()
            ->selectDistinctYears()
            ->orderByYear();
    }
    
    public function getControllerName()
    {
        return 'ArmdChronicleBundle:Event';
    }
}

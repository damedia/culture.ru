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
        return $this->render($this->getTemplateName('chronicle'), array(
            'century'   => $century,
            'events'    => $this->getEventsList($century),
        ));
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
        $centuries = $this->getCenturiesRepository()->getQuery()->getArrayResult();
        
        foreach ($centuries as $c) {
            $value = Converter::toRoman($c['century']);           
            $result[] = array(
                'value'     => $value,
                'selected'  => $value == $century,
            );
        }
        
        return $result;
    }
    
    function getEventsList($century)
    {
        return $this->getEventsRepository($century)->getQuery()->getResult();
    }
    
    function getCenturiesRepository()
    {
        return $this->getListRepository()
            ->selectDistinctCenturies()
            ->orderByCentury();
    }
    
    function getEventsRepository($century)
    {
        return $this->getListRepository()
            ->setCentury(Converter::toNumber($century))
            ->orderByYear();
    }
    
    public function getControllerName()
    {
        return 'ArmdChronicleBundle:Event';
    }
}

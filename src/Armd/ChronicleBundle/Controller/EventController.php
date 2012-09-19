<?php

namespace Armd\ChronicleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Armd\ListBundle\Controller\ListController;
use Armd\ChronicleBundle\Util\RomanConverter as Converter;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventController extends ListController
{
    
	function __construct(ContainerInterface $container = null)
    {
        $this->setContainer($container);    
    }
	
	
	/**
     * @Route("/", name="armd_chronicle_index")     
     */
	public function indexAction()
    {
        return $this->render($this->getTemplateName('chronicle'), array(
            'centuries' => $this->getCenturiesList(),
        ));
    }
        
    function getCenturiesList()
    {
        $result = array();
        $centuries = $this->getCenturiesRepository()->getQuery()->getArrayResult();
        
        foreach ($centuries as $c) {
            $century = $c['century'];
            
            $result[] = array(
                'value'     => $century,
                'name'      => Converter::toRoman($century),
                'events'    => $this->getEventsList($century),
                'accidents' => $this->getAccidentsList($century),
            );
        }
        
        return $result;
    }
    
    function getEventsList($century)
    {
        return $this->getEventsRepository($century)->getQuery()->getResult();
    }
    
    function getAccidentsList($century)
    {
        return $this->getDoctrine()->getRepository('ArmdChronicleBundle:Accident')->findBy(array('event' => null, 'century' => $century), array('date' => 'ASC'));
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
            ->setCentury($century)
            ->orderByDate('DESC');
    }
    
    public function getControllerName()
    {
        return 'ArmdChronicleBundle:Event';
    }
}

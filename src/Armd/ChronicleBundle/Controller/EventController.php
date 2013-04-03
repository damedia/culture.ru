<?php

namespace Armd\ChronicleBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\ListBundle\Controller\ListController;
use Armd\ChronicleBundle\Util\RomanConverter as Converter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Sonata\MediaBundle\Model\Media;

class EventController extends ListController
{  
    function __construct(ContainerInterface $container = null)
    {
        $this->setContainer($container);    
    }
	
	
    /**
     * @Route("/{century}/{part}", 
     * defaults={"century" = "21", "part" = "0"}, 
     * requirements={"century"="\d+", "part"="\d+"}, 
     * name="armd_chronicle_index")     
     */
    public function indexAction($century = 0, $part = 0)
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_chronicle_index')
        );

        $activeCentury = $activePart = false;
        $centuries = array();
        $centuriesResult = $this->getCenturiesList();
        
        if (!$century && count($centuriesResult)) {
            $century =  $centuriesResult[0]['value'];
        }                       
        
        foreach ($centuriesResult as $c) {
            if ($c['eventsCount'] > 100) {
                $c['parts'] = array(
                    0 => array('value' => 2, 'name' => Converter::toRoman(2) . ' половина'),
                    1 => array('value' => 1, 'name' => Converter::toRoman(1) . ' половина')
                );
            }                                 
            
            if ($c['value'] == $century) {
                $activeCentury = $c;
            }
            
            $centuries[$c['value']] = $c;
        }
        
        if (!$activeCentury) {                     
            throw $this->createNotFoundException(sprintf('Unable to find century %d', $century));
        }
        
        if (isset($centuries[$century]['parts'])) {                      
            if (!$part) {
                $activePart = $centuries[$century]['parts'][0];
            } else {
                foreach ($centuries[$century]['parts'] as $p) {
                    if ($part == $p['value']) {
                        $activePart = $p;
                    }
                }
            }
            
            if (!$activePart) {                     
                throw $this->createNotFoundException(sprintf('Unable to find part %d', $part));
            }
        }
        
        return $this->render($this->getTemplateName('chronicle'), array( 
            'centuries' => $centuries,
            'activeCentury' => $activeCentury,
            'activePart' => $activePart,
            'width' => '100%',
            'height' => 600,
            'lang' => 'ru',
            'start_at_end' => true,
            'start_zoom_adjust' => 0
        ));
    }


    /**
     * @Route("/all/", name="armd_chronicle_all")
     * @Template()
    */
     public function allAction()
     {
        $activeCentury = $activePart = false;
        return  array(
            'centuries' => $this->getCenturiesListAll(),
            'activeCentury' => $activeCentury,
            'activePart' => $activePart,
        );
     }

    
    /**
     * @Route("/timeline_json_data.json/{century}/{part}", 
     * defaults={"century" = "21", "part" = "0"}, 
     * requirements={"century"="\d+", "part"="\d+"},
     * name="armd_chronicle_timeline_json_data")     
     */
    public function timelineJsonDataAction($century = 0, $part = 0)
    {
        $result = array();
        $result['timeline'] = array(
            'headline' => '',
            'type' => 'default',
            'startDate' => '',
            'text' => ''
        );
        
        $events = $this->getEventsList($century, $part);
        
        foreach ($events as $event) {
            if (get_class($event) == 'Armd\ChronicleBundle\Entity\Event') {
                $result['timeline']['date'][] = array(
                    'startDate' => $event->getDate()->format('Y,n,j'),
                    'headline' => $event->getTitle(),
                    'text' => $event->getBody(),
                    'accident' => '',
                    'asset' => array(
                        'media' => $event->getImage() ? $this->getMediaUrl($event->getImage(), 'list') : '',
                        'thumbnail' => $event->getImage() ? $this->getMediaUrl($event->getImage(), 'adminPreview') : '',
                        'credit' => '',
                        'caption' => $event->getImage() ? $event->getImage()->getTitle() : ''
                    )
                );
            } else {
                $result['timeline']['date'][] = array(
                    'startDate' => $event->getDate()->format('Y,n,j'),
                    'headline' => 'В мире',
                    'text' => $event->getAnnounce(),
                    'accident' => 'accident',
                    'asset' => array(
                        'media' => '',
                        'thumbnail' => '',
                        'credit' => 'В мире',
                        'caption' => 'В мире'
                    )
                );
            }
        }       
        
        return new JsonResponse($result);
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
                'eventsCount'    => $this->getEventsCount($century),
                'events' => $this->getEventsListAll($century),
 	  	'accidents' => $this->getAccidentsList($century),
            );
        }
        
        return $result;
    }


    function getCenturiesListAll()
    {
        $result = array();
        $centuries = $this->getCenturiesRepository()->getQuery()->getArrayResult();

        foreach ($centuries as $c) {
            $century = $c['century'];

            $result[] = array(
                'value'     => $century,
                'name'      => Converter::toRoman($century),
                'events' => $this->getEventsListAll($century),
         	  	'accidents' => $this->getAccidentsList($century),
            );
        }

        return $result;
    }

    function getAccidentsList($century)
    {
         return $this->getDoctrine()->getRepository('ArmdChronicleBundle:Accident')->findBy(array('event' => null, 'century' => $century), array('date' => 'ASC'));
    }

    function getEventsListAll($century)
    {
        return $this->getEventsRepository($century)->getQuery()->getResult();
    }

    function getEventsList($century, $part = 0)
    {
        $repository = $this->getEventsRepository($century);
        $accidentRepository = $this->getAccidentsRepository($century);
        
        if ($part) {
            if ($part == 1) {
                $from = new \DateTime(($century - 1) . '00-01-01');
                $to = new \DateTime(($century - 1) . '50-01-01');
            } else {
                $from = new \DateTime(($century - 1) . '50-01-01');
                $to = new \DateTime($century . '00-01-01');
            }
            
            $repository->setPeriod($from, $to);
            $accidentRepository->setPeriod($from, $to);
        }
        
        $eventsAccidents = array_merge(
            $repository->getQuery()->getResult(), 
            $accidentRepository->getQuery()->getResult()
        );
        
        usort($eventsAccidents, function ($a, $b) {
            if ($a->getDate() > $b->getDate()) {
                return -1;
            } elseif ($a->getDate() < $b->getDate()) {
                return 1;
            } else {
                return 0;
            }
        });
        
        return $eventsAccidents;
    }
    
    function getEventsCount($century)
    {
        $result = $this->getListRepository()->setCentury($century)
            ->selectCount()->getQuery()->getSingleResult();

        return $result['cnt'];
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
    
    function getAccidentsRepository($century)
    {
        $repository = $this->getDoctrine()->getEntityManager()
                ->getRepository('ArmdChronicleBundle:Accident');
        
        $repository->createQueryBuilder('t');
        
        return $repository->setCentury($century)->orderByDate('DESC');       
    }
    
    public function getControllerName()
    {
        return 'ArmdChronicleBundle:Event';
    }
    
    /**
     * @param \Sonata\MediaBundle\Model\Media $media
     * @return string
     */
    public function getMediaUrl(Media $media, $format)
    {
        /**
         * @var \Sonata\MediaBundle\Provider\Pool $mediaService
         */
        $mediaService = $this->container->get('sonata.media.pool');
        $provider = $mediaService->getProvider($media->getProviderName());
        $format = $provider->getFormatName($media, $format);

        return $provider->generatePublicUrl($media, $format);
    }
}

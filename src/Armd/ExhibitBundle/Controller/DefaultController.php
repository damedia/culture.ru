<?php

namespace Armd\ExhibitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    private $limit = 9;
    
    protected function getImageSrc(\Application\Sonata\MediaBundle\Entity\Media $image)
    {
        $mediaPool = $this->get('sonata.media.pool');
        $provider = $mediaPool->getProvider($image->getProviderName());
        
        return $provider->getCdnPath($provider->getReferenceImage($image), $image->getCdnIsFlushable());
    }
    
    protected function getObjects($limit = 0, $offset = 0)
    {
        $data = array();
        $repository = $this->getDoctrine()->getRepository('ArmdExhibitBundle:ArtObject');
        $repository->createQueryBuilder('o');
        $entities = $repository
            ->setLimit($limit, $offset)
            ->getQuery()
            //->getSQL();
            ->getResult();
        //\Doctrine\Common\Util\Debug::dump($entities); die();
        foreach ($entities as $i => $e) {
            $data[$i] = array(
                'img' => $this->getImageSrc($e->getImage()),
                'title' => $e->getTitle(),
                'date' => $e->getDate()->format('Y'),
                'museum' => array('id' => $e->getMuseum()->getId(), 'title' => $e->getMuseum()->getTitle())
            );
            
            foreach ($e->getAuthors() as $a) {
                $data[$i]['authors'][] = array('id' => $a->getId(), 'title' => $a->getName());
            }
        }
        
        return $data;
    }
            
    /**
     * @Route("list", name="armd_exhibit_list")
     * @Template("ArmdExhibitBundle:Default:exhibit_list.html.twig")
     */
    public function listAction()
    {      
        return array(
            'data' => json_encode(array('data' => $this->getObjects($this->limit), 'offset' => $this->limit))
        );
    }
    
    /**
     * @Route("load-exhibits/{offset}", requirements={"offset"="\d+"}, 
     *      defaults={"offset"=0}, name="armd_load_exhibits", options={"expose"=true}
     * )
     */
    public function loadExhibitsAction($offset = 0)
    {                    
        return new JsonResponse(
            array(
                'data' => $this->getObjects($this->limit, $offset), 
                'offset' => $offset + $this->limit
            )
        );
    }
}
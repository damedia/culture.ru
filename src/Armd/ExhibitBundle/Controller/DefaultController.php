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
    
    protected function getObjects($filters = array(), $limit = 0, $offset = 0)
    {
        $data = $fAutor = $fMuseum = $fCategory = array();
        $repository = $this->getDoctrine()->getRepository('ArmdExhibitBundle:ArtObject');
        $repository->createQueryBuilder('o');
        
        foreach ($filters as $k => $v) {
            if ($k == 'author') {
                $fAutor = array_keys($v);
            } elseif ($k == 'museum') {
                $fMuseum = array_keys($v);
            } elseif (intval($k)) {
                $fCategory = array_merge($fCategory, array_keys($v));
            }
        }
        
        if (count($fAutor)) {
            $repository->setAuthors($fAutor);
        }
        
        if (count($fMuseum)) {
            $repository->setMuseums($fMuseum);
        }
        
        if (count($fCategory)) {
            $repository->setCategories($fCategory);
        }
        
        $entities = $repository
            ->setDistinct()
            ->setPublished()
            ->setLimit($limit, $offset)
            ->orderByDate()
            ->getQuery()
            ->getResult();
        //\Doctrine\Common\Util\Debug::dump($entities);
        //die();
        foreach ($entities as $i => $e) {
            $data[$i] = array(
                'img' => $this->getImageSrc($e->getImage()),
                'title' => $e->getTitle(),
                'date' => $e->getDate()->format('Y'),
                'museum' => array('id' => $e->getMuseum()->getId(), 'title' => $e->getMuseum()->getTitle()),
                'authors' => array()
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
        $filters = array(
            'author' => array('title' => 'Автор'),
            'museum' => array('title' => 'Музей')
        );
        
        $authors = $this->getDoctrine()->getRepository('ArmdPersonBundle:Person')
            ->createQueryBuilder('a')
            ->join('a.personTypes', 't')
            ->andWhere('t.slug = :slug')->setParameter('slug', 'art_gallery_author')
            ->getQuery()->getResult();       
        
        foreach ($authors as $a) {
            $filters['author']['data'][] = array('id' => $a->getId(), 'title' => $a->getName());
        }
        
        $museums = $this->getDoctrine()->getRepository('ArmdMuseumBundle:RealMuseum')
            ->findBy(array(), array('title' => 'ASC'));
        
        foreach ($museums as $m) {
            $filters['museum']['data'][] = array('id' => $m->getId(), 'title' => $m->getTitle());
        }
        
        $categories = $this->getDoctrine()->getRepository('ArmdExhibitBundle:Category')->getArrayTree();
        
        foreach ($categories as $c) {
            $filters[$c['id']] = array(
                'title' => $c['title'],
                'data' => array()
            );
            
            foreach ($c['children'] as $ch) {
                $filters[$c['id']]['data'][] = array('id' => $ch['id'], 'title' => $ch['title']);
            }
        }
        
        return array(
            'data' => array('objects' => $this->getObjects(array(), $this->limit), 'offset' => $this->limit),
            'filters' => $filters
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
                'objects' => $this->getObjects(
                    $this->getRequest()->request->get('filters', array()), 
                    $this->limit, 
                    $offset
                ), 
                'offset' => $offset + $this->limit
            )
        );
    }
}
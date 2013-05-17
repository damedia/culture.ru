<?php

namespace Armd\ExhibitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Armd\ExhibitBundle\Repository\ArtObjectRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    protected $limit = 50;
    
    protected function getImageSrc(\Application\Sonata\MediaBundle\Entity\Media $image = null, $format = 'reference')
    {
        if (!$image) {
            return '';
        }
        
        $mediaPool = $this->get('sonata.media.pool');
        $provider = $mediaPool->getProvider($image->getProviderName());
        
        if ($format != 'reference') {
            $format = $provider->getFormatName($image, $format);       
        }
        
        return $provider->generatePublicUrl($image, $format);
    }
    
    protected function setFilters(ArtObjectRepository $repository, $filters)
    {
        $fAutor = $fMuseum = $fCategory = array();
        
        if (isset($filters['search'])) {
            $repository->setSearch($filters['search']);
        } else {
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
        }
    }
    
    protected function getObjects($filters = array(), $limit = 0, $offset = 0)
    {
        $data = array('objects' => array(), 'count' => 0);
        $repository = $this->getDoctrine()->getRepository('ArmdExhibitBundle:ArtObject');
        $repository->createQueryBuilder('o');      
        $this->setFilters($repository, $filters);              
        
        if ($offset == 0) {
            $data['count'] = $repository->getCount();
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
            $data['objects']["'{$e->getId()}'"] = array(
                'id' => $e->getId(),
                'img' => $this->getImageSrc($e->getImage(), 'default'),    
                'img_width' => $e->getImage()->getWidth(),
                'img_height' => $e->getImage()->getHeight(),
                'title' => $e->getTitle(),
                'date' => $e->getTextDate(),
                'museum' => array('id' => '', 'title' => ''),
                'authors' => array()
            );
            
            if ($e->getMuseum()) {
                $data['objects']["'{$e->getId()}'"]['museum'] = array('id' => $e->getMuseum()->getId(), 'title' => $e->getMuseum()->getTitle());
            }
            
            foreach ($e->getAuthors() as $a) {
                $data['objects']["'{$e->getId()}'"]['authors'][] = array('id' => $a->getId(), 'title' => $a->getName());
            }
        }
        
        return $data;
    }
    
    protected function getItemObjects($filters = array(), $limit = 0, $offset = 0, $firstId = 0)
    {
        $data = array('objects' => array(), 'count' => 0);
        $repository = $this->getDoctrine()->getRepository('ArmdExhibitBundle:ArtObject');
        $repository->createQueryBuilder('o');      
        $this->setFilters($repository, $filters);   
        
        if ($offset == 0) {
            $repoCount = clone $repository;
            $data['count'] = $repoCount->getCount();
        }
        
        if ($firstId) {
            $repository->setNotId($firstId);
        }
                
        $entities = $repository
            ->setDistinct()
            ->setPublished()
            ->setLimit($limit, $offset)
            ->orderByDate()
            ->getQuery()
            ->getResult();       
        
        if ($firstId && $offset == 0) {
             $object = $this->getDoctrine()->getRepository('ArmdExhibitBundle:ArtObject')->find($firstId);
        
            if ($object) {
                $entities = array_merge(array($object), $entities);
            }
        }
        
        foreach ($entities as $i => $e) {
            $data['objects']["'{$e->getId()}'"] = array(
                'id' => $e->getId(),
                'img' => $this->getImageSrc($e->getImage()),
                'img_thumb' => $this->getImageSrc($e->getImage(), 'smallZoom'),
                'img_small' => $this->getImageSrc($e->getImage(), 'small'),
                'title' => $e->getTitle(),
                'date' => $e->getTextDate(),
                'description' => $e->getDescription(),
                'museum' => array(
                    'id' => '', 
                    'title' => '',
                    'address' => '',
                    'url' => '',
                    'img' => '',
                    'vtour' => array()
                ),               
                'authors' => array(),
                'video' => array()
            );
            
            if ($e->getMuseum()) {
                $data['objects']["'{$e->getId()}'"]['museum'] = array(
                    'id' => $e->getMuseum()->getId(), 
                    'title' => $e->getMuseum()->getTitle(),
                    'address' => $e->getMuseum()->getAddress(),
                    'url' => $e->getMuseum()->getUrl(),
                    'img' => $this->getImageSrc($e->getMuseum()->getImage(), 'realSmall')
                );                               
            }
            
            if ($e->getVirtualTour()) {
                $vTour = $e->getVirtualTour();
                
                if ($e->getVirtualTourUrl()) {
                    $data['objects']["'{$e->getId()}'"]['virtual_tour']['url'] = $e->getVirtualTourUrl();
                } else {
                    $data['objects']["'{$e->getId()}'"]['virtual_tour']['url'] = $vTour->getUrl();
                }
                
                $data['objects']["'{$e->getId()}'"]['virtual_tour']['img'] = $this->getImageSrc($vTour->getImage());
            } elseif ($e->getMuseum() && $e->getMuseum()->getVirtualTours()->count()) {
                $vTour = $e->getMuseum()->getVirtualTours()->first();
                $data['objects']["'{$e->getId()}'"]['virtual_tour']['url'] = $vTour->getUrl();
                $data['objects']["'{$e->getId()}'"]['virtual_tour']['img'] = $this->getImageSrc($vTour->getImage());
            }
            
            foreach ($e->getAuthors() as $a) {
                $data['objects']["'{$e->getId()}'"]['authors'][] = array(
                    'id' => $a->getId(), 
                    'title' => $a->getName(),
                    'description' => $a->getDescription(),
                    'life_dates' => $a->getLifeDates(),
                    'image' => $this->getImageSrc($a->getImage(), 'small')
                );
            }
            
            if ($e->getVideos()->count()) {
                $video = $e->getVideos()->first();
                
                if ($video->getFrame()) {
                    $data['objects']["'{$e->getId()}'"]['video']['img'] = $video->getImage();
                    $data['objects']["'{$e->getId()}'"]['video']['frame'] = $video->getFrame();
                }
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
        
        $activeFilters = $this->get('session')->get('exhibits-filters', array());
        
        $authors = $this->getDoctrine()->getRepository('ArmdPersonBundle:Person')
            ->createQueryBuilder('a')
            ->join('a.personTypes', 't')
            ->andWhere('t.slug = :slug')->setParameter('slug', 'art_gallery_author')
            ->getQuery()->getResult();       
        
        foreach ($authors as $a) {
            $filters['author']['data']["{$a->getId()}"] = array('id' => $a->getId(), 'title' => $a->getName());
        }
        
        $museums = $this->getDoctrine()->getRepository('ArmdMuseumBundle:RealMuseum')
            ->findBy(array(), array('title' => 'ASC'));
        
        foreach ($museums as $m) {
            $filters['museum']['data']["{$m->getId()}"] = array('id' => $m->getId(), 'title' => $m->getTitle());
        }
        
        $categories = $this->getDoctrine()->getRepository('ArmdExhibitBundle:Category')->getArrayTree();
        
        foreach ($categories as $c) {
            if (isset($c['children'])) {
                $filters[$c['id']] = array(
                    'title' => $c['title'],
                    'data' => array()
                );

                foreach ($c['children'] as $ch) {
                    $filters[$c['id']]['data']["{$ch['id']}"] = array('id' => $ch['id'], 'title' => $ch['title']);
                }
            }
        }
        
        $objects = $this->getObjects($activeFilters, $this->limit);
        
        return array(
            'data' => array('objects' => $objects['objects'], 'count' => $objects['count'], 'offset' => $this->limit),
            'filters' => $filters,
            'activeFilters' => $activeFilters
        );
    }

    /**
     * @Route("item/{id}", name="armd_exhibit_item", options={"expose"=true})
     */
    public function itemAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('ArmdExhibitBundle:ArtObject');
        $repository->createQueryBuilder('o');
        $repository->setPublished();

        if (!$repository->findOneById($id)) {
            throw $this->createNotFoundException('ArtObject not found');
        }

        return new RedirectResponse($this->generateUrl('armd_exhibit_list') ."#{$id}");
    }
    
    /**
     * @Route("load-exhibits/{offset}", requirements={"offset"="\d+"}, 
     *      defaults={"offset"=0}, name="armd_exhibit_load_exhibits", options={"expose"=true}
     * )
     */
    public function loadExhibitsAction($offset = 0)
    {
        $filters = $this->getRequest()->request->get('filters', array());
        $this->get('session')->set('exhibits-filters', $filters);      
        $objects = $this->getObjects($filters, $this->limit, $offset);
        
        return new JsonResponse(
            array(
                'objects' => $objects['objects'],
                'count' => $objects['count'],
                'offset' => $offset + $this->limit
            )
        );
    }
    
    /**
     * @Route("load-exhibit/{id}", requirements={"id"="\d+"}, name="armd_exhibit_load_item", options={"expose"=true})
     */
    public function loadItemAction($id)
    {       
        $objects = $this->getItemObjects(
            $this->get('session')->get('exhibits-filters', array()),
            $this->limit,
            0,
            $id
        );               
        
        return new JsonResponse(
            array(
                'objects' => $objects['objects'],
                'count' => $objects['count'],
                'offset' => $this->limit,
                'id' => $id
            )
        );
    }
    
    /**
     * @Route("load-exhibit-objects/{id}/{offset}", requirements={"offset"="\d+", "id"="\d+"}, 
     *      defaults={"offset"=0, "id"=0}, name="armd_exhibit_load_item_objects", options={"expose"=true}
     * )
     */
    public function loadItemObjectsAction($id = 0, $offset = 0)
    {            
        $objects = $this->getItemObjects(
            $this->get('session')->get('exhibits-filters', array()),
            $this->limit, 
            $offset,
            $id
        );
        
        return new JsonResponse(
            array(
                'objects' => $objects['objects'],
                'count' => $objects['count'],
                'offset' => $offset + $this->limit,
            )
        );
    }
}
<?php

namespace Armd\MuseumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\MuseumBundle\Entity\MuseumManager;

class DefaultController extends Controller
{

    /**
     * @Route("/virtual/", name="armd_museum_virtual")
     * @Template("ArmdMuseumBundle:Default:virtual.html.twig")
     */
    public function virtualAction()
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_museum_virtual')
        );

        $regionId = (int) $this->getRequest()->get('region', 0);
        $regions = $this->getMuseumManager()->getDistinctRegions();
        $categories = $this->getMuseumManager()->getCategories();

        return array(
            'regionId' => $regionId,
            'regions' => $regions,
            'categories' => $categories,
        );
    }

    /**
     * @Route("/list", name="armd_museum_list", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Default:virtual_list.html.twig")
     */
    public function listAction()
    {

        $regionId = $this->getRequest()->get('region');
        $categoryId = $this->getRequest()->get('category');
        $searchText = $this->getRequest()->get('search_query');

        $criteria = array();
        
        if (!empty($categoryId)) {
            $criteria[MuseumManager::CRITERIA_CATEGORY_IDS_OR] = array($categoryId);
        }

        if (!empty($regionId)) {
            $criteria[MuseumManager::CRITERIA_REGION_IDS_OR] = array($regionId);
        }
        
        if (!empty($searchText)) {
            $criteria[MuseumManager::CRITERIA_SEARCH_STRING] = $searchText;
        }

        $museums = $this->getMuseumManager()->findObjects($criteria);
        
        if (count($museums)) {
            $by_region = array();
            $regions = $this->getMuseumManager()->getDistinctRegions();
            
            foreach ($regions as $region) {
                $by_region[$region['id']] = array(
                    'region' => $region,
                    'list' => array()
                );
            }
            
            foreach ($museums as $museum) {
                if ($museum->getRegion())
                    array_push($by_region[$museum->getRegion()->getId()]['list'], $museum);
            }
            
            foreach ($by_region as $key => $region) {
                if (!count($region['list']))
                    unset($by_region[$key]);
            }
        }

        return array(
            'museums' => $museums,  
            'by_region' => $by_region
        );

    }

    
    /**
     * @Route("/guide", name="armd_museum_guide_index")
     * @Template()
     */
    public function guideIndexAction()
    {
        $cityId = (int) $this->getRequest()->get('cityId', 0);
        $museumId = (int) $this->getRequest()->get('museumId', 0);
        $cities = $this->getGuideCities();
        $museums = $this->getGuideMuseums();
        
        return array(
            'museums' => $museums,
            'cities' => $cities,
            'cityId' => $cityId,
            'museumId' => $museumId,
        );
    }
    
    /**
     * @Route("/guide/{id}", requirements={"id" = "\d+"}, name="armd_museum_guide_item")
     * @Template()
     */
    public function guideItemAction($id)
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri($this->generateUrl('armd_museum_guide_index'));
        
        if (null === ($entity = $this->getMuseumGuideRepository()->find($id))) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $id));
        }
        
        $cities = $this->getGuideCities();
        $museums = $this->getGuideMuseums();
        
        return array(
            'entity' => $entity,
            'museums' => $museums,
            'cities' => $cities,
        );
    }
    
    /**
     * @Route("/guide/list", name="armd_museum_guide_list", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Default:guideList.html.twig")
     */
    public function guideListAction()
    {
        $findBy = array();
        if(($cityId = (int)$this->getRequest()->get('cityId')) > 0) {
            $findBy['city'] = $cityId;
        }
        if(($museumId = (int)$this->getRequest()->get('museumId')) > 0) {
            $findBy['museum'] = $museumId;
        }
        
        $museumGuides = $this->getMuseumGuideRepository()->findBy($findBy, array('title' => 'ASC'));
        return array(
            'museumGuides' => $museumGuides,  
        );
    }
    
    /**
     * @Route("/guide/see-also", name="armd_museum_guide_see_also")
     * @Template("ArmdMuseumBundle:Default:guideSeeAlso.html.twig")
     */
    public function guideSeeAlsoAction()
    {
        $request = $this->getRequest();
        $entityId = $request->get('id');
        $limit = $request->get('limit', 3);

        $museumGuides = $this->getNearestGuides($entityId, $limit);

        return array(
            'museumGuides' => $museumGuides,
        );
    }
    
    /**
     * @Route("/guide/cities", name="armd_museum_guide_cities", options={"expose": true})
     */
    public function filterCityAction()
    {
        $cities = $this->getGuideCities();
        
        return new JsonResponse($cities);
    }
    
    /**
     * @Route("/guide/museums", name="armd_museum_guide_museums", defaults={"cityId"=0}, options={"expose": true})
     */
    public function filterMuseumAction($cityId = 0)
    {
        $realMuseums = $this->getGuideMuseums($cityId);
        
        return new JsonResponse($realMuseums);
    }


    /**
     * @return \Armd\MuseumBundle\Entity\MuseumManager
     */
    protected function getMuseumManager()
    {
        return $this->get('armd_museum.manager.museum');
    }

    /**
     * @param string $action
     * @param array $params
     * @return array
     */
    public function getItemsSitemap($action = null, $params = array())
    {
        $items = array();

        switch ($action) {
            case 'indexAction': {
                if ($museums = $this->getMuseumManager()->findObjects(array())) {
                    foreach ($museums as $m) {
                        $atlasObject = $m->getAtlasObject();

                        $items[] = array(
                            'loc' => $this->generateUrl('armd_atlas_default_object_view', array(
                                'id' => $m->getAtlasObject()->getId()
                            )),
                            'lastmod' => $atlasObject ? $atlasObject->getUpdatedAt() : null
                        );
                    }
                }

                break;
            }
        }

        return $items;
    }


    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getMuseumGuideRepository()
    {
        return $this->getDoctrine()->getEntityManager()->getRepository('ArmdMuseumBundle:MuseumGuide');
    }
    
    /**
     * @return array
     */
    protected function getNearestGuides($entityId, $limit)
    {
        $qb = $this->getMuseumGuideRepository()->createQueryBuilder('g');
        $qb->select('g, abs(g.id - :entityId) as HIDDEN sort')
            ->andWhere('g.id <> :entityId')
            ->addOrderBy('sort', 'ASC')
            ->setParameter(':entityId', $entityId)
            ->setMaxResults($limit);
        return $qb->getQuery()->getResult();
    }
    
    /**
     * @return array
     */
    protected function getGuideCities()
    {
        $qb = $this->getDoctrine()->getEntityManager()->getRepository('ArmdAddressBundle:City')->createQueryBuilder('c');
        $qb->select('c')
                ->join('ArmdMuseumBundle:MuseumGuide', 'g', 'WITH', 'g.city = c.id')
                ->orderBy('c.sortIndex');
        return $qb->getQuery()->getArrayResult();
    }
    
    /**
     * @param integer $cityId
     * @return array
     */
    protected function getGuideMuseums($cityId = 0)
    {
        $qb = $this->getDoctrine()->getEntityManager()->getRepository('ArmdMuseumBundle:RealMuseum')->createQueryBuilder('m');
        $qb->select('m')->join('ArmdMuseumBundle:MuseumGuide', 'g', 'WITH', 'g.museum = m.id');
        /*
        if((int)$cityId > 0) {
            $qb->andWhere('g.city = :cityId');
            $qb->setParameter(':cityId', $cityId);
        }
        */
        $qb->orderBy('m.title');
        return $qb->getQuery()->getResult();
    }
}

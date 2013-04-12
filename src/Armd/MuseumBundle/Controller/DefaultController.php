<?php

namespace Armd\MuseumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
     * @Route("/edu", name="armd_museum_edu", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Default:edu.html.twig")
     */
    public function eduAction()
    {
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
     * @Route("/eduone", name="armd_museum_edu_one", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Default:edu_one.html.twig")
     */
    public function eduoneAction()
    {
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
     * @Route("/guide", name="armd_museum_guide", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Default:guide.html.twig")
     */
    public function guideAction()
    {
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
     * @Route("/guide/", name="armd_museum_guide_index")
     * @Template()
     */
    public function guideIndexAction()
    {
        return array();
    }
    
    /**
     * @Route("/guide/{id}", requirements={"id" = "\d+"}, name="armd_museum_guide_item")
     * @Template()
     */
    public function guideItemAction($id)
    {
        if (null === ($entity = $this->getMuseumGuideRepository()->find($id))) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $id));
        }
        
        return array(
            'entity' => $entity
        );
    }
    
    /**
     * @Route("/guide/list", name="armd_museum_guide_list", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Default:guideList.html.twig")
     */
    public function guideListAction()
    {
            return array(
                'museumGuides' => $this->getMuseumGuideRepository()->findBy(
                        array(), 
                        array('title' => 'ASC')
                    ),  
            );
    }


    /**
     * @return \Armd\MuseumBundle\Entity\MuseumManager
     */
    protected function getMuseumManager()
    {
        return $this->get('armd_museum.manager.museum');
    }

    protected function getMuseumGuideRepository()
    {
        return $this->getDoctrine()->getEntityManager()->getRepository('ArmdMuseumBundle:MuseumGuide');
    }
}

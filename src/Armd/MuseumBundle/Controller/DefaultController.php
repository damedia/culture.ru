<?php

namespace Armd\MuseumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\MuseumBundle\Entity\MuseumManager;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="armd_museum_index")
     * @Template()
     */
    public function indexAction()
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
     * @Route("/virtual/", name="armd_museum_virtual")
     * @Template("ArmdMuseumBundle:Default:index.html.twig")
     */
    public function virtualAction()
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_museum_virtual')
        );        
        return $this->indexAction();
    }    

    /**
     * @Route("/list", name="armd_museum_list", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Default:list.html.twig")
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
     * @return \Armd\MuseumBundle\Entity\MuseumManager
     */
    protected function getMuseumManager()
    {
        return $this->get('armd_museum.manager.museum');
    }

}

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
     * @Route("/list", name="armd_museum_list", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Default:list.html.twig")
     */
    public function listAction()
    {
//        try {
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

            return array(
                'museums' => $museums,  
            );
//        }
//        catch (\Exception $e) {
//            $this->createNotFoundException('Not found');
//        }
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
     * @Route("/guide/list", name="armd_museum_guide_list", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Default:guideList.html.twig")
     */
    public function guideListAction()
    {
            return array(
                'museumGuides' => $this->findMuseumGuides(),  
            );
    }

    /**
     * @return \Armd\MuseumBundle\Entity\MuseumManager
     */
    protected function getMuseumManager()
    {
        return $this->get('armd_museum.manager.museum');
    }

    protected function findMuseumGuides($criteria = array())
    {
        return $this->getDoctrine()->getEntityManager()
                ->getRepository('ArmdMuseumBundle:MuseumGuide')->findBy($criteria, array('title' => 'ASC'));
    }
}

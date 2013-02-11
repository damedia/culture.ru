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
        try {
            $regionId = $this->getRequest()->get('region');
            $categoryId = $this->getRequest()->get('category');

            $criteria = array();

            if (!empty($categoryId)) {
                $criteria[MuseumManager::CRITERIA_CATEGORY_IDS_OR] = array($categoryId);
            }

            if (!empty($regionId)) {
                $criteria[MuseumManager::CRITERIA_REGION_IDS_OR] = array($regionId);
            }

            $museums = $this->getMuseumManager()->findObjects($criteria);

            return array(
                'museums' => $museums,
            );
        }
        catch (\Exception $e) {
            $this->createNotFoundException('Not found');
        }
    }

    /**
     * @return \Armd\MuseumBundle\Entity\MuseumManager
     */
    protected function getMuseumManager()
    {
        return $this->get('armd_museum.manager.museum');
    }

}

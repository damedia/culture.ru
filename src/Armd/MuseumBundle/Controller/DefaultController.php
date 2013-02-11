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
        $regions = $this->getMuseumManager()->getRegions();
        $categories = $this->getMuseumManager()->getCategories();

        return array(
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
            $filter = $this->getRequest()->get('f');

            $criteria = array(
                //MuseumManager::CRITERIA_IS_ON_MAP => true,
                //MuseumManager::CRITERIA_HAS_IMAGE => true,
                //MuseumManager::CRITERIA_EVENT_DATE_SINCE => empty($filter['date_from']) ? new DateTime : new DateTime($filter['date_from']),
                //MuseumManager::CRITERIA_EVENT_DATE_TILL => empty($filter['date_to']) ? new DateTime : new DateTime($filter['date_to']),
            );

            if (!empty($filter['category'])) {
                $criteria[MuseumManager::CRITERIA_CATEGORY_IDS_OR] = array($filter['category']);
            }

            if (!empty($filter['region'])) {
                $criteria[MuseumManager::CRITERIA_REGION_IDS_OR] = array($filter['region']);
            }

            $museums = $this->getMuseumManager()->findObjects($criteria);

            return array(
                'museums' => $museums,
            );
        }
        catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
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

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
}

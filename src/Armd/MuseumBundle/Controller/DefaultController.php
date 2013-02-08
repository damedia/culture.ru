<?php

namespace Armd\MuseumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="armd_museum_index")
     * @Template()
     */
    public function indexAction()
    {
        return array(
        );
    }

    /**
     * @Route("/museums-list", name="armd_museum_list", options={"expose"=true})
     * @Template()
     */
    public function listAction($page = 1, $perPage = 1000)
    {
        $museumManager = $this->get('armd_museum.manager.museum');
        $criteria = array(
            'search_string' => $this->getRequest()->get('searchString')
        );
        return array(
            'museums' => $museumManager->getPager($criteria, $page, $perPage),
        );
    }

    /**
     * @Route("/filter", name="armd_museum_filter", defaults={"_format"="json"})
     */
    public function filterAction()
    {
        try {
            $filter = $this->getRequest()->get('f');

            $criteria = array(
                NewsManager::CRITERIA_IS_ON_MAP => true,
                NewsManager::CRITERIA_HAS_IMAGE => true,
                NewsManager::CRITERIA_EVENT_DATE_SINCE => empty($filter['date_from']) ? new DateTime : new DateTime($filter['date_from']),
                NewsManager::CRITERIA_EVENT_DATE_TILL => empty($filter['date_to']) ? new DateTime : new DateTime($filter['date_to']),
            );

            if (!empty($filter['category'])) {
                $criteria[NewsManager::CRITERIA_CATEGORY_IDS_OR] = array($filter['category']);
            } else {
                throw new \Exception('Выберите хотя бы один тип события.');
            }

            $museums = $this->getNewsManager()->findObjects($criteria);

            $data = array();

            return array(
                'success' => true,
                'message' => 'OK',
                'result' => array(
                    'filter' => $filter,
                    'data' => $data,
                ),
            );
        }
        catch (\Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
            );
        }
    }
}

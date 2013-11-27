<?php

namespace Armd\MuseumBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\MuseumBundle\Entity\MuseumManager;

class DefaultController extends Controller {
    const PALETTE_COLOR_HEX = '#349A8A';

    private $palette_color = 'palette-color-3';

    /**
     * @Route("/virtual", name="armd_museum_virtual")
     * @Template("ArmdMuseumBundle:Museums:museumsIndex.html.twig")
     */
    public function virtualAction() {
        $regionId = (int)$this->getRequest()->get('region', 0);
        $regions = $this->getMuseumManager()->getDistinctRegions();
        $categories = $this->getMuseumManager()->getCategories();

        return array(
            'palette_color' => $this->palette_color,
            'palette_color_hex' => DefaultController::PALETTE_COLOR_HEX,
            'regionId' => $regionId,
            'regions' => $regions,
            'categories' => $categories
        );
    }

    /**
     * @Route("/museums-indexWidget", name="armd_museum_indexWidget", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Museums:museums_indexWidget.html.twig")
     */
    public function museumsIndexWidgetAction($date = '') {
        $repo = $this->getDoctrine()->getRepository('ArmdMuseumBundle:Museum');
        $museums = $repo->findForMain($date, 5);

        return array(
            'museums' => $museums,
            'palette_color' => $this->palette_color
        );
    }

    /**
     * @Route("/museums-indexSidebar", name="armd_museum_indexSidebar", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Museums:museums_indexSidebar.html.twig")
     */
    public function sidebarIndexWidget() {
        return array();
    }

    /**
     * This is a terrible temporary stub for not having an attribute flag "museum_reserved" in
     * the Museum entity (and therefore we can't really have a legitimate way to get just these
     * kind of museums). I was in a hurry and instead of modifying the database I just created
     * this method to replace even more terrible thing - hardcoded HTML UL...
     */
    private function getReserveMuseums() {
        $criteria = array();
        $reserveMuseums = array(27, 16, 15, 21, 18, 10, 26, 5, 24, 22);

        $criteria[MuseumManager::CRITERIA_IDS_OR] = $reserveMuseums;
        $museums = $this->getMuseumManager()->findObjects($criteria);

        return $museums;
    }

    /**
     * @Route("/museum_reserve", name="armd_main_museum_reserve", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Museums:museum_reserve.html.twig")
     */
    public function museumReserveAction() {
        return array('museum_reserve' => $this->getReserveMuseums());
    }

    /**
     * @Route("/museum_reserve_sidebar_list", name="armd_main_museum_reserve_sidebar_list", options={"expose"=true})
     * @Template("ArmdMuseumBundle:Museums:museum_reserve_sidebar_list.html.twig")
     */
    public function museumReserveListAction() {
        return array('museum_reserve' => $this->getReserveMuseums());
    }

    /**
     * @Route("/list/{limit}", name="armd_museum_list", options={"expose"=true}, defaults={"limit"="8"})
     * @Template("ArmdMuseumBundle:Museums:museums_virtualList.html.twig")
     */
    public function listAction($limit = 8) {
        $criteria = array();
        $request = $this->getRequest();

        $loadedIds = $request->get('loadedIds');
        if ($loadedIds) {
            $criteria[MuseumManager::CRITERIA_NOT_IDS] = array_unique($loadedIds);
        }

        $categoryId = $request->get('category');
        if ($categoryId) {
            $criteria[MuseumManager::CRITERIA_CATEGORY_IDS_OR] = array($categoryId);
        }

        $regionId = $request->get('region');
        if ($regionId) {
            $criteria[MuseumManager::CRITERIA_REGION_IDS_OR] = array($regionId);
        }

        $searchText = $request->get('search_query');
        if ($searchText) {
            $criteria[MuseumManager::CRITERIA_SEARCH_STRING] = $searchText;
        }

        //$criteria[MuseumManager::CRITERIA_ORDER_BY] = array('sort' => 'DESC', 'title' => 'ASC');
        $criteria[MuseumManager::CRITERIA_LIMIT] = $limit;
        $criteria[MuseumManager::CRITERIA_RANDOM] = true;

        $museums = $this->getMuseumManager()->findObjects($criteria);

        /*
        if (count($museums)) {
            $by_region = array();
            $regions = $this->getMuseumManager()->getDistinctRegions();

            foreach ($regions as $region) {
                $by_region[$region['id']] = array('region' => $region, 'list' => array());
            }

            foreach ($museums as $museum) {
                if ($museum->getRegion()){
                    array_push($by_region[$museum->getRegion()->getId()]['list'], $museum);
                }
            }

            foreach ($by_region as $key => $region) {
                if (!count($region['list'])) {
                    unset($by_region[$key]);
                }
            }
        }
        */

        return array('museums' => $museums, /*'by_region' => $by_region*/);
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
        return $this->getDoctrine()->getRepository('ArmdMuseumBundle:MuseumGuide');
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
        $qb = $this->getDoctrine()->getRepository('ArmdAddressBundle:City')->createQueryBuilder('c');
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
        $qb = $this->getDoctrine()->getRepository('ArmdMuseumBundle:RealMuseum')->createQueryBuilder('m');
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

    /**
     * @Route("/archive", name="armd_museum_archive")
     *
     */
    public function archiveAction()
    {
        return $this->render("ArmdMuseumBundle:Default:archive.html.twig");
    }

    /**
     * @param string $date
     * @Template()
     * @return array
     */
    public function mainpageWidgetAction($date = '')
    {
        /** @var \Armd\MuseumBundle\Repository\MuseumRepository $repo */
        $repo = $this->getDoctrine()->getRepository('ArmdMuseumBundle:Museum');
        $museums = $repo->findForMain($date, 3);

        if($this->getRequest()->isXmlHttpRequest()) {
            return $this->render(
                'ArmdMuseumBundle:Default:mainpageWidgetItem.html.twig',
                array('museums' => $museums)
            );
        } else {
            return $this->render(
                'ArmdMuseumBundle:Default:mainpageWidget.html.twig',
                array('museums' => $museums)
            );
        }
    }
}

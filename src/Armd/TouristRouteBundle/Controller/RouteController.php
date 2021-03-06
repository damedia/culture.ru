<?php

namespace Armd\TouristRouteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteController extends Controller
{
    /**
     * @Route("/", name="armd_tourist_route_list", options={"expose"=true})
     * @Template("ArmdTouristRouteBundle:Default:list.html.twig")
     */
    public function listAction()
    {
        $entities = $this->getRoutes(array('published' => true));

        return array(
            'entities'    => $entities,
            'regions'     => $this->getRegions(),
            'regionId'    => $this->getRequest()->get('route_region'),
            'categories'  => $this->getCategories(),
            'categoryId'  => $this->getRequest()->get('route_category'),
            'searchQuery' => $this->getRequest()->get('search_query')
        );
    }

    /**
     * @Route("/_ajax_tourist_route_list", name="armd_tourist_route_ajax_list", options={"expose"=true})
     * @Template("ArmdTouristRouteBundle:Default:ajaxList.html.twig")
     */
    public function ajaxListAction($offset = 0, $limit = 30)
    {
        // fix menu
        $this->get('armd_main.menu.main')->setCurrentUri(
            $this->get('router')->generate('armd_tourist_route_list')
        );

        $entities = $this->getObjects('ArmdTouristRouteBundle:Route', array(
            'published'   => true,
            'region_id'   => $this->getRequest()->get('region_id'),
            'category_id' => $this->getRequest()->get('category_id'),
            'search_text' => $this->getRequest()->get('search_text')

        ), $limit, $offset);

        return array('entities' => $entities);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"}, name="armd_tourist_route_item")
     * @Template("ArmdTouristRouteBundle:Default:item.html.twig")
     */
    public function itemAction($id)
    {
        $entity = $this->getDoctrine()
            ->getEntityManager()
                ->getRepository('ArmdTouristRouteBundle:Route')
                    ->findOneBy(array(
                        'id'        => $id,
                        'published' => true
                    ));

        if (!$entity) {
            throw new NotFoundHttpException("Tourist route with ID = {$id} not found");
        }
        
        return array('entity' => $entity);
    }

    /**
     * @Route("/{id}/print", requirements={"id"="\d+"}, name="armd_tourist_route_print_item")
     * @Template("ArmdTouristRouteBundle:Default:printItem.html.twig")
     */
    public function printItem($id)
    {
        return $this->itemAction($id);
    }

    /**
     * Get entity repository
     *
     * @param  string  $className
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository($className)
    {
        return $this->getDoctrine()
            ->getEntityManager()
                ->getRepository($className);
    }

    /**
     * Get routes
     *
     * @param  array    $criteria
     * @param  integer  $limit
     * @param  integer  $offset
     * @param  array    $orderBy
     * @return array
     */
    protected function getRoutes($criteria = array(), $limit = 30, $offset = 0, $orderBy = null)
    {
        return $this->getObjects('ArmdTouristRouteBundle:Route', $criteria, $limit, $offset, $orderBy);
    }

    /**
     * Get regions
     *
     * @return array
     */
    protected function getRegions()
    {
        $routeRepository = $this->getRepository('ArmdTouristRouteBundle:Route');
        $queryBuilder    = $routeRepository->createQueryBuilder('r');

        $queryBuilder
            ->select('DISTINCT reg.id, reg.title')
            ->leftJoin('r.regions', 'reg')
            ->orderBy('reg.title');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Get categories
     *
     * @return array
     */
    protected function getCategories()
    {
        return $this->getRepository('ArmdTouristRouteBundle:Category')
            ->findAll();
    }

    /**
     * Get objects
     *
     * @param  string   $className
     * @param  array    $criteria
     * @param  integer  $limit
     * @param  integer  $offset
     * @param  array    $orderBy
     * @return array
     */
    protected function getObjects($className, $criteria = array(), $limit = null, $offset = null, $orderBy = null)
    {
        $repository   = $this->getRepository($className);
        $queryBuilder = $repository->createQueryBuilder('t');

        foreach ($criteria as $key=>$val) {
            if (!empty($val)) {
                switch ($key) {
                    case 'published': {
                        $queryBuilder
                            ->andWhere('t.published = :published')
                            ->setParameter('published', $val);
                        break;
                    }
                    case 'region_id': {
                        $queryBuilder
                            ->leftJoin('t.regions', 'r')
                            ->andWhere('r.id = :region_id')
                            ->setParameter('region_id', $val);
                        break;
                    }

                    case 'category_id': {
                        $queryBuilder
                            ->leftJoin('t.categories', 'c')
                            ->andWhere('c.id = :category_id')
                            ->setParameter('category_id', $val);
                        break;
                    }

                    case 'search_text': {
                        $queryBuilder->andWhere(
                            $queryBuilder->expr()->like(
                                $queryBuilder->expr()->lower('t.title'),
                                '\'%' .mb_strtolower($val, 'UTF8') .'%\''
                            )
                        );
                        break;
                    }
                }
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }
}

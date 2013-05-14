<?php

namespace Armd\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AjaxController extends Controller
{
    /**
     * @Route(
     *     "/_ajax_list/{class}/{property}/{page}/{limit}",
     *     name="armd_util_ajax_list",
     *     defaults={"_format"="json", "page"=1, "limit"=20},
     *     options={"expose"=true}
     * )
     */
    public function listAction($class, $property, $page, $limit)
    {
        if (false === $this->isGranted('ROLE_ADMIN')) {
            return array('error' => '401 Access denied');
        }

        $term         = $this->getRequest()->get('q');
        $fields       = array_merge(array('id'), $this->parsePropertiesString($property));
        $queryBuilder = $this->getRepository($class)->createQueryBuilder('t');

        if ($term and $fields) {
            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->like(
                        $queryBuilder->expr()->lower('t.' .$fields[1]),
                        '\'%' .mb_strtolower($term, 'UTF8') .'%\''
                    )
                );
        }

        $total = $queryBuilder
            ->select('COUNT(t.id) as total')
            ->getQuery()->getSingleResult();
        $total = $total['total'];
        
        $queryBuilder
            ->select('t.' .implode(', t.', $fields))
            ->setFirstResult(((int) $page - 1) * $limit)
            ->setMaxResults($limit);

        $entities = $queryBuilder->getQuery()->getResult();
        $result   = array(
            'page'     => (int) $page,
            'pages'    => ceil($total / $limit),
            'total'    => $total,
            'entities' => $entities
        );

        return $result;
    }

    /**
     * @Route(
     *     "/_ajax_list_by_ids/{class}/{property}/{ids}",
     *     name="armd_util_ajax_list_by_ids",
     *     defaults={"_format"="json"},
     *     options={"expose"=true}
     * )
     */
    public function listByIdsAction($class, $property, $ids)
    {
        if (false === $this->isGranted('ROLE_ADMIN')) {
            return array('error' => '401 Access denied');
        }

        if (is_string($ids)) {
            $ids = array_map('trim', explode(',', $ids));
        }

        $fields       = array_merge(array('id'), $this->parsePropertiesString($property));
        $queryBuilder = $this->getRepository($class)->createQueryBuilder('t');

        $queryBuilder
            ->select('t.' .implode(', t.', $fields))
            ->andWhere($queryBuilder->expr()->in('t.id', $ids));
        $entities = $queryBuilder->getQuery()->getResult();

        return array('entities' => $entities);
    }

    /**
     * @Route(
     *     "_ajax_item/{class}/{property}/{id}",
     *     name="armd_util_ajax_item",
     *     defaults={"_format"="json"},
     *     options={"expose"=true}
     * )
     */
    public function itemAction($class, $property, $id)
    {
        if (false === $this->isGranted('ROLE_ADMIN')) {
            return array('error' => '401 Access denied');
        }

        $fields       = array_merge(array('id'), $this->parsePropertiesString($property));
        $queryBuilder = $this->getRepository($class)->createQueryBuilder('t');

        $queryBuilder
            ->select('t.' .implode(', t.', $fields))
            ->andWhere('t.id = :id')
            ->setParameter('id', $id);
        $entity = $queryBuilder->getQuery()->getOneOrNullResult();

        if (!$entity) {
            return array('error' => '404 Not found');
        }

        return array('entity' => $entity);
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function getRepository($class)
    {
        return $this->getDoctrine()
            ->getManager()
                ->getRepository($class);
    }

    /**
     * @param string $property
     * @return array
     */
    protected function parsePropertiesString($property)
    {
        return array_map('trim', explode(',', $property));
    }

    /**
     * @param string $role
     * @return bool
     */
    public function isGranted($role)
    {
        return $this->container->get('security.context')->isGranted($role);
    }
}

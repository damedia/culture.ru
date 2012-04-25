<?php

namespace Armd\CultureAreaBundle\Controller;

use Armd\Bundle\CmsBundle\Controller\Controller;
use Armd\Bundle\CmsBundle\UsageType\UsageType;

class CultureAreaController extends Controller
{
    /**
     * @param Armd\Bundle\CmsBundle\UsageType\UsageType $params
     * @param $name
     * @return mixed
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $params = $this->getRequestParams();
        $id = $params['id'];

        // корневые категории
        $dql = "SELECT a.id, a.lvl, a.lft, a.rgt, a.title
                FROM Armd\CultureAreaBundle\Entity\CultureArea a
                WHERE a.lvl = 1
                ORDER BY a.lft ASC";
        $query = $em->createQuery($dql);
        $categories = $query->getResult();

        // путь до выбранной категории
        $dql = "SELECT a.id, a.lvl, a.lft, a.rgt, a.title
                FROM Armd\CultureAreaBundle\Entity\CultureArea a, Armd\CultureAreaBundle\Entity\CultureArea b
                WHERE b.id=:id AND b.lft BETWEEN a.lft AND a.rgt
                ORDER BY a.lft ASC";
        $query = $em->createQuery($dql)->setParameter('id', $id);
        $parents = $query->getResult();

        $path = array();
        foreach ($parents as $node) {
            $path[] = $node['id'];
        }

        // текущая корневая категория
        $category = $parents[1];

        // узлы выбранной категории
        $dql = "SELECT a.id, a.lvl, a.lft, a.rgt, a.title
                FROM Armd\CultureAreaBundle\Entity\CultureArea a
                WHERE (a.lvl = 1 \n";
        foreach ($parents as $i=>$node) {
            // if (sizeof($parents)-1 == $i) break; // показывать дочерние узлы текущего узла
            $lvl = $i+1;
            $lft = (int) $node['lft'];
            $rgt = (int) $node['rgt'];
            $dql .= " OR (a.lvl = $lvl AND a.lft > $lft AND a.rgt < $rgt) \n";
        }
        $dql.= ")  AND a.lvl != 1 "; // исключаем корневые категории
        $dql.= " ORDER BY a.lft ASC";
        $query = $em->createQuery($dql);
        $nodes = $query->getResult();

        return $this->renderCms(array(
            'categories' => $categories,
            'category' => $category,
            'nodes' => $nodes,
            'path' => $path,
            'id' => $id,
        ));
    }


    public function mainAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $dql = "SELECT a FROM Armd\CultureAreaBundle\Entity\CultureArea a WHERE a.lvl=:lvl ORDER BY a.lft DESC";
        $query = $em->createQuery($dql)
                    ->setParameter('lvl', 1);
        $entities = $query->getResult();
        return $this->renderCms(array(
            'entities' => $entities,
        ));
    }

    public function itemAction($id)
    {
        $entity = $this->getEntityRepository()->find($id);

        if (! $entity) {
            throw $this->createNotFoundException('Unable to find entity');
        }

        return $this->renderCms(array('entity' => $entity));
    }

}

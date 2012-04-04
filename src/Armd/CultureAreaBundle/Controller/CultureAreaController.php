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
        $id = 42;
        $request = $this->get('request');
        if (preg_match("/q\/item-(\d+)$/", $request->getPathInfo(), $m)) {
            $id = (int) $m[1];
        }

        $em = $this->getDoctrine()->getEntityManager();

        // 1. Получим текущий узел и его left_key, right_key
        $dql = "SELECT a.lft, a.rgt, a.lvl FROM Armd\CultureAreaBundle\Entity\CultureArea a WHERE a.id=:id";
        $query = $em->createQuery($dql)
                    ->setParameter('id', $id);
        $node = $query->getSingleResult();

        // 2. Получаем дерево узлов с раскрытой веткой
        $dql = "SELECT a.id, a.lft, a.rgt, a.lvl, a.title
                FROM Armd\CultureAreaBundle\Entity\CultureArea a
                WHERE a.parent IN (
                  SELECT b.id FROM Armd\CultureAreaBundle\Entity\CultureArea b
                  WHERE b.rgt>:lft AND b.lft<:rgt
                ) OR a.lvl=:lvl
                ORDER BY a.lft ASC";
        $query = $em->createQuery($dql)
                    ->setParameters(array(
                        'lft' => $node['lft'],
                        'rgt' => $node['rgt'],
                        'lvl' => 1,
                    ));
        $nodes = $query->getResult();
        $content = $this->renderTree($nodes, $id);

        return $this->renderCms(array(
            'content' => $content,
            'nodes' => $nodes,
            'id' => $id,
        ));
    }

    public function mainAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $dql = "SELECT a.id, a.title FROM Armd\CultureAreaBundle\Entity\CultureArea a WHERE a.lvl=:lvl ORDER BY a.lft ASC";
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

    protected function renderTree($data=array(), $selectedId=0)
    {
        $html = '';
        $level = 0;
        foreach ($data as $n=>$node) {
            if ($node['lvl'] == $level)
                $html .= '</li>';
            else if ($node['lvl'] > $level)
                $html .= '<ul>';
            else {
                $html .= '</li>';
                for ($i = $level - $node['lvl']; $i; $i--) {
                    $html .= '</ul>';
                    $html .= '</li>';
                }
            }
            $html .= '<li>';
            if ($selectedId == $node['id']) {
                $html .= $node['title'];
            } else {
                $html .= '<a href="#">'.$node['title'].'</a>';
            }
            $level = $node['lvl'];
        }
        for ($i = $level; $i; $i--) {
            $html .= '</li>';
            $html .= '</ul>';
        }
        return $html;
    }
}

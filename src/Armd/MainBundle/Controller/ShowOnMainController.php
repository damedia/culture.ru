<?php

namespace Armd\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ShowOnMainController extends Controller
{
    protected function getFieldClass($field)
    {
        $fields = \Armd\MainBundle\Admin\ShowOnMain::getFields();
        
        if (isset($fields[$field])) {
            return $fields[$field];
        } else {
            return '';
        }
    }
    
    /**
     * @Route("get-values/{field}", defaults={"field"=""}, 
     *      name="armd_show_on_main_values", options={"expose"=true}
     * )
     */
    public function getValues($field = '')
    {
        $class = $this->getFieldClass($field);
        
        if (!$class) {
            if (preg_match("~^news-(\d+)$~", $field, $matches)) {
                $class = 'Armd\NewsBundle\Entity\News';
                $category = $matches[1];
                $cName = 'category';
            } elseif (preg_match("~^lectures-(\d+)$~", $field, $matches)) {
                $class = 'Armd\LectureBundle\Entity\Lecture';
                $category = $matches[1];
                $cName = 'lectureSuperType';
            } else {
                return new JsonResponse(array());
            }
        }
        
        $qb = $this->getDoctrine()->getRepository($class)->createQueryBuilder('t')
            ->select('t.id, t.title AS text')
            ->where('t.published = TRUE')
            ->andWhere('t.showOnMain = TRUE')
            ->orderBy('t.title', 'ASC')
            ->setMaxResults(100);
        
        if (isset($category)) {
            $qb->andWhere('t.' . $cName . ' = :category')->setParameter('category', $category);
        }
        
        return new JsonResponse($qb->getQuery()->getScalarResult());
    }
    
    /**
     * @Route("get-list", name="armd_show_on_main_list", options={"expose"=true}
     * )
     */
    public function getList()
    {
        $field = $this->getRequest()->get('field', '');
        $class = $this->getFieldClass($field);
        $search = $this->getRequest()->get('search', '');       
        $pageLimit = $this->getRequest()->get('pageLimit', 20);
        $page = $this->getRequest()->get('page', 1);

        if (!$class) {
            if (preg_match("~^news-(\d+)$~", $field, $matches)) {
                $class = 'Armd\NewsBundle\Entity\News';
                $category = $matches[1];
                $cName = 'category';
            } elseif (preg_match("~^lectures-(\d+)$~", $field, $matches)) {
                $class = 'Armd\LectureBundle\Entity\Lecture';
                $category = $matches[1];
                $cName = 'lectureSuperType';
            } else {
                return new JsonResponse(array('result' => array(), 'total' => 0));
            }           
        }
        
        $qb = $this->getDoctrine()
            ->getRepository($class)
            ->createQueryBuilder('t')           
            ->where('t.published = TRUE');
        
        if ($search) {
            $qb->andWhere("LOWER(t.title) LIKE :search")->setParameter('search', "%" . mb_strtolower($search, 'UTF-8') . "%");
        }
        
        if (isset($category)) {
            $qb->andWhere('t.' . $cName . ' = :category')->setParameter('category', $category);
        }
        
        $qbCount = clone $qb;
        $total = $qbCount->select('COUNT(t.id) AS total')->getQuery()->getSingleScalarResult();
        
        $result = $qb->select('t.id, t.title AS text')            
            ->orderBy('t.title')
            ->setMaxResults($pageLimit)
            ->setFirstResult($pageLimit * ($page - 1))
            ->getQuery()
            ->getScalarResult();
        
        return new JsonResponse(array('result' => $result, 'total' => $total));
    }
}

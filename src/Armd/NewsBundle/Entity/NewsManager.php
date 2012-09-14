<?php

namespace Armd\NewsBundle\Entity;

use Doctrine\ORM\EntityManager;

use Sonata\DoctrineORMAdminBundle\Datagrid\Pager;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class NewsManager
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     * @param string                      $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em = $em;
        $this->class = $class;
    }

    public function getPager(array $criteria, $page, $limit = 10)
    {
        return $this->getQueryBuilder($criteria)
            ->getQuery()
            ->setMaxResults($limit)
            ->getResult()
        ;
    }
    
    public function getQueryBuilder(array $criteria)
    {
        $parameters = array();

        $query = $this->em->getRepository($this->class)
            ->createQueryBuilder('n')
            ->select('n, c, i')
            ->innerJoin('n.category', 'c', 'WITH', 'c.slug = :slug')
            ->leftJoin('n.image', 'i', 'WITH', 'i.enabled = true')
            ->andWhere('n.published = true')
            ->orderBy('n.date', 'DESC')
        ;

        $parameters['slug'] = $criteria['category'];
        
        if (isset($criteria['borodino'])) {
            $query->andWhere('n.borodino = true');
        }
                
        $query->setParameters($parameters);        
        
        return $query;        
    }        
}

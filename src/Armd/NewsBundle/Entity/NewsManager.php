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

    /**    
     * @param array $criteria
     * @param integer $page
     * @param integer $maxPerPage
     *
     * @return \Sonata\AdminBundle\Datagrid\Pager
     */
    public function getPager(array $criteria, $page, $maxPerPage = 10)
    {
        $pager = new Pager();
        $pager->setMaxPerPage($maxPerPage);
        $pager->setQuery(new ProxyQuery($this->getQueryBuilder($criteria)));
        $pager->setPage($page);
        $pager->init();

        return $pager;
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
//            ->orderBy('n.date', 'DESC')
        ;

        $parameters['slug'] = $criteria['category'];
        
        if (isset($criteria['tag']) && 'borodino' == $criteria['tag']) {
            $query->andWhere('n.borodino = true');
        }
                
        $query->setParameters($parameters);        
        
        return $query;        
    }        
}

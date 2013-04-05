<?php

namespace Armd\TheaterBundle\Entity;

use Armd\SphinxSearchBundle\Services\Search\SphinxSearch;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Armd\TagBundle\Entity\TagManager;
use Armd\ListBundle\Entity\ListManager;


class TheaterManager extends ListManager
{
    private $search;

    /** example: array(1, 89) */
    const CRITERIA_CATEGORY_IDS_OR = 'CRITERIA_CATEGORY_IDS_OR';
    
    /** example: array(1, 89) */
    const CRITERIA_CITY_IDS_OR = 'CRITERIA_CITY_IDS_OR';    

    /** example: 'the rolling stones' */
    const CRITERIA_SEARCH_STRING = 'CRITERIA_SEARCH_STRING';


    public function __construct(EntityManager $em, TagManager $tagManager, SphinxSearch $search)
    {
        parent::__construct($em, $tagManager);
        $this->search = $search;
    }

    public function findObjects(array $criteria) {
        if (!empty($criteria[self::CRITERIA_SEARCH_STRING])) {
            return $this->findObjectsWithSphinx($criteria);
        } else {
            return parent::findObjects($criteria);
        }
    }

    public function getQueryBuilder()
    {
        $qb = $this->em->getRepository('ArmdTheaterBundle:Theater')->createQueryBuilder('t')
            ->select('DISTINCT t')
            ->innerJoin('t.categories', 'c')
            ->innerJoin('t.image', 'i')
            ->where('t.published = TRUE');

        return $qb;
    }

    public function setCriteria(QueryBuilder $qb, $criteria)
    {
        parent::setCriteria($qb, $criteria);

        $aliases = $qb->getRootAliases();
        $a = $aliases[0];

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_OR])) {
            $qb
                ->andWhere("c IN (:categoryIds)")
                ->setParameter('categoryIds', $criteria[self::CRITERIA_CATEGORY_IDS_OR]);
        }       

        if (!empty($criteria[self::CRITERIA_CITY_IDS_OR])) {
            $qb->andWhere("{$a}.city IN (:cityIds)")
                ->setParameter('cityIds', $criteria[self::CRITERIA_CITY_IDS_OR]);
        }                      
    }   

    public function findObjectsWithSphinx($criteria) 
    {
        $searchParams = array('Theater' => array('filters' => array()));

        if (isset($criteria[self::CRITERIA_LIMIT])) {
            $searchParams['Theater']['result_limit'] = (int) $criteria[self::CRITERIA_LIMIT];
        }

        if (isset($criteria[self::CRITERIA_OFFSET])) {
            $searchParams['Theater']['result_offset'] = (int) $criteria[self::CRITERIA_OFFSET];
        }       

        $searchResult = $this->search->search($criteria[self::CRITERIA_SEARCH_STRING], $searchParams);

        $result = array();
        
        if (!empty($searchResult['Theater']['matches'])) {
            $repo = $this->em->getRepository('ArmdTheaterBundle:Theater');
            $result = $repo->findBy(array('id' => array_keys($searchResult['Theater']['matches'])));
        }

        return $result;
    }
    
    public function getObject($id)
    {
        $entity = $this->em->getRepository('ArmdTheaterBundle:Theater')->createQueryBuilder('t')
            ->select('t, b')
            ->leftJoin('t.billboards', 'b', 'WITH', 'b.date > :now')->setParameter('now', new \DateTime())
            ->andWhere('t.published = :published')->setParameter('published', true)
            ->andWhere('t.id = :id')->setParameter('id', $id)
            ->addOrderBy('b.date')
            ->getQuery()
            ->getSingleResult()
        ;       

        return $entity;
    }

    public function getClassName()
    {
        return 'Armd\TheaterBundle\Entity\Theater';
    }

    public function getTaggableType()
    {
        return 'armd_theater_theater';
    }
}

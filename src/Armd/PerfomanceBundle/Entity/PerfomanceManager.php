<?php
namespace Armd\PerfomanceBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Armd\TagBundle\Entity\TagManager;
use Armd\ListBundle\Entity\ListManager;
use Armd\SphinxSearchBundle\Services\Search\SphinxSearch;
use Knp\Component\Pager\Paginator;

class PerfomanceManager extends ListManager
{
    protected $search;

    /** example: array(1, 2) */
    const CRITERIA_GANRE_IDS_OR = 'CRITERIA_GANRE_IDS_OR';

    /** example: 'the rolling stones' */
    const CRITERIA_SEARCH_STRING = 'CRITERIA_SEARCH_STRING';
    
    /** example: 'A' */
    const CRITERIA_FIRST_LETTER = 'CRITERIA_FIRST_LETTER';  
    
    /** example: array(1, 2) */
    const CRITERIA_THEATER_IDS_OR = 'CRITERIA_THEATER_IDS_OR';

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
        $qb = $this->em->getRepository($this->getClassName())
            ->createQueryBuilder('_perfomance')
            ->andWhere('_perfomance.published = TRUE');

        return $qb;
    }

    public function setCriteria(QueryBuilder $qb, $criteria) {
        parent::setCriteria($qb, $criteria);

        if (!empty($criteria[self::CRITERIA_GANRE_IDS_OR])) {
            $qb->innerJoin('_perfomance.ganres', '_perfomanceGanres')
                ->andWhere('_perfomanceGanres IN (:ganre_ids_or)')
                ->setParameter('ganre_ids_or', $criteria[self::CRITERIA_GANRE_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_FIRST_LETTER])) {
            $qb->andWhere("SUBSTRING(TRIM(LEADING ' .\"«' FROM _perfomance.title), 1, 1) = :first_letter")
                ->setParameter('first_letter', $criteria[self::CRITERIA_FIRST_LETTER]);
        }
        
        if (!empty($criteria[self::CRITERIA_THEATER_IDS_OR])) {
            $qb->andWhere("_perfomance.theater IN (:theater_ids_or)")
                ->setParameter('theater_ids_or', $criteria[self::CRITERIA_THEATER_IDS_OR]);
        }
    }

    public function findObjectsWithSphinx($criteria) {
        $searchParams = array('Perfomances' => array('filters' => array()));

        if (isset($criteria[self::CRITERIA_LIMIT])) {
            $searchParams['Perfomances']['result_limit'] = (int) $criteria[self::CRITERIA_LIMIT];
        }

        if (isset($criteria[self::CRITERIA_OFFSET])) {
            $searchParams['Perfomances']['result_offset'] = (int) $criteria[self::CRITERIA_OFFSET];
        }

        if (!empty($criteria[self::CRITERIA_GANRE_IDS_OR])) {
            $searchParams['Perfomances']['filters'][] = array(
                'attribute' => 'perfomanceganre_id',
                'values' => $criteria[self::CRITERIA_GANRE_IDS_OR]
            );
        }

        $searchResult = $this->search->search($criteria[self::CRITERIA_SEARCH_STRING], $searchParams);

        $result = array();
        if (!empty($searchResult['Perfomances']['matches'])) {
            $repository = $this->em->getRepository('ArmdPerfomanceBundle:Perfomance');
            $ids = array_keys($searchResult['Perfomances']['matches']);
            foreach ($ids as $id) {
                $entity = $repository->find($id);
                if (!empty($entity)) {
                    $result[] = $entity;
                }
            }
        }

        return $result;
    }


    public function getClassName()
    {
        return 'Armd\PerfomanceBundle\Entity\Perfomance';
    }

    public function getTaggableType()
    {
        return 'armd_perfomance';
    }
}
<?php
namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Armd\TagBundle\Entity\TagManager;
use Armd\ListBundle\Entity\ListManager;
use Armd\SphinxSearchBundle\Services\Search\SphinxSearch;
use Knp\Component\Pager\Paginator;

class LessonManager extends ListManager
{
    protected $search;

    /** example: 'the rolling stones' */
    const CRITERIA_SEARCH_STRING = 'CRITERIA_SEARCH_STRING';
    
    /** example: array(1, 2) */
    const CRITERIA_SKILL_IDS_OR = 'CRITERIA_SKILL_IDS_OR';    
    
    /** example: 1 */
    const CRITERIA_MUSEUM_ID = 'CRITERIA_MUSEUM_ID';    
    
    /** example: 1 */
    const CRITERIA_CITY_ID = 'CRITERIA_CITY_ID';        
    
    /** example: 1 */
    const CRITERIA_SUBJECT_ID = 'CRITERIA_SUBJECT_ID';        
    
    /** example: 1 */
    const CRITERIA_EDUCATION_ID = 'CRITERIA_EDUCATION_ID';        
    
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
            ->createQueryBuilder('_lesson')
            ->andWhere('_lesson.published = TRUE');

        return $qb;
    }

    public function setCriteria(QueryBuilder $qb, $criteria) {
        parent::setCriteria($qb, $criteria);

        if (!empty($criteria[self::CRITERIA_SKILL_IDS_OR])) {
            $qb->innerJoin('_lesson.skills', '_lessonSkills')
                ->andWhere('_lessonSkills IN (:skill_ids_or)')
                ->setParameter('skill_ids_or', $criteria[self::CRITERIA_SKILL_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_MUSEUM_ID])) {
            $qb->andWhere("_lesson.museum = :museum_id")
                ->setParameter('museum_id', $criteria[self::CRITERIA_MUSEUM_ID]);
        }               
        
        if (!empty($criteria[self::CRITERIA_CITY_ID])) {
            $qb->andWhere("_lesson.city = :city_id")
                ->setParameter('city_id', $criteria[self::CRITERIA_CITY_ID]);
        }                       
        
        if (!empty($criteria[self::CRITERIA_EDUCATION_ID])) {
            $qb->andWhere("_lesson.education = :education_id")
                ->setParameter('education_id', $criteria[self::CRITERIA_EDUCATION_ID]);
        }                       
        
        if (!empty($criteria[self::CRITERIA_SUBJECT_ID])) {
            $qb->andWhere("_lesson.subject = :subject_id")
                ->setParameter('subject_id', $criteria[self::CRITERIA_SUBJECT_ID]);
        }                       
    }

    public function findObjectsWithSphinx($criteria) {
        $searchParams = array('Lessons' => array('filters' => array()));

        if (isset($criteria[self::CRITERIA_LIMIT])) {
            $searchParams['Lessons']['result_limit'] = (int) $criteria[self::CRITERIA_LIMIT];
        }

        if (isset($criteria[self::CRITERIA_OFFSET])) {
            $searchParams['Lessons']['result_offset'] = (int) $criteria[self::CRITERIA_OFFSET];
        }

        if (!empty($criteria[self::CRITERIA_SKILL_IDS_OR])) {
            $searchParams['Lessons']['filters'][] = array(
                'attribute' => 'lessonskill_id',
                'values' => $criteria[self::CRITERIA_SKILL_IDS_OR]
            );
        }
        
        if (!empty($criteria[self::CRITERIA_MUSEUM_ID])) {
            $searchParams['Lessons']['filters'][] = array(
                'attribute' => 'museum_id',
                'values' => $criteria[self::CRITERIA_MUSEUM_ID]
            );
        }        
        
        if (!empty($criteria[self::CRITERIA_CITY_ID])) {
            $searchParams['Lessons']['filters'][] = array(
                'attribute' => 'city_id',
                'values' => $criteria[self::CRITERIA_CITY_ID]
            );
        }                
        
        if (!empty($criteria[self::CRITERIA_EDUCATION_ID])) {
            $searchParams['Lessons']['filters'][] = array(
                'attribute' => 'education_id',
                'values' => $criteria[self::CRITERIA_EDUCATION_ID]
            );
        }   

        if (!empty($criteria[self::CRITERIA_SUBJECT_ID])) {
            $searchParams['Lessons']['filters'][] = array(
                'attribute' => 'subject_id',
                'values' => $criteria[self::CRITERIA_SUBJECT_ID]
            );
        }        

        $searchResult = $this->search->search($criteria[self::CRITERIA_SEARCH_STRING], $searchParams);

        $result = array();
        if (!empty($searchResult['Lessons']['matches'])) {
            $repository = $this->em->getRepository('ArmdMuseumBundle:Lesson');
            $ids = array_keys($searchResult['Lessons']['matches']);
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
        return 'Armd\MuseumBundle\Entity\Lesson';
    }

    public function getTaggableType()
    {
        return 'armd_lesson';
    }
}
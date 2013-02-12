<?php
namespace Armd\LectureBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Armd\TagBundle\Entity\TagManager;
use Armd\ListBundle\Entity\ListManager;
use Armd\LectureBundle\Entity\LectureSuperType;
use Armd\SphinxSearchBundle\Services\Search\SphinxSearch;
use Knp\Component\Pager\Paginator;

class LectureManager extends ListManager
{
    protected $search;


    const CRITERIA_SUPER_TYPE_CODES_OR = 'CRITERIA_SUPER_TYPE_CODES_OR';

    public function __construct(EntityManager $em, TagManager $tagManager, SphinxSearch $search)
    {
        parent::__construct($em, $tagManager);
        $this->search = $search;
    }

    public function getQueryBuilder()
    {
        $qb = $this->em->getRepository($this->getClassName())
            ->createQueryBuilder('_lecture')
            ->andWhere('_lecture.published = TRUE');

        return $qb;
    }

    public function setCriteria(QueryBuilder $qb, $criteria) {
        parent::setCriteria($qb, $criteria);

        if (!empty($criteria[self::CRITERIA_SUPER_TYPE_CODES_OR])) {
            $qb->innerJoin('_lecture.lectureSuperType', '_lectureSuperType')
                ->andWhere('_lectureSuperType.code IN (:super_type_codes_or)')
                ->setParameter('super_type_codes_or', $criteria[self::CRITERIA_SUPER_TYPE_CODES_OR]);
        }
    }


    public function findFiltered($superTypeId = null, $page = 1, $perPage = 20, $typeIds = null, $categoryIds = null, $sortBy = 'date', $searchString = '')
    {
        $searchParams = array(
            'Lectures' => array(
                'result_offset' => ($page - 1) * $perPage,
                'result_limit' => (int) $perPage,
                'sort_mode' => '@relevance DESC, @weight DESC, date_from DESC',
            )
        );

        // add filters
        if (isset($superTypeId)) {
            $searchParams['Lectures']['filters'] = array(
                array(
                    'attribute' => 'lecture_super_type_id',
                    'values' => array($superTypeId),
                )
            );
        }

        // search by text with Sphinx
        if (is_array($typeIds)) {
            $searchParams['Lectures']['filters'][] = array(
                'attribute' => 'lecture_type_id',
                'values' => $typeIds
            );
        }

        if (is_array($categoryIds)) {
            $searchParams['Lectures']['filters'][] = array(
                'attribute' => 'lecturecategory_id',
                'values' => $categoryIds
            );
        }

        $searchResult = $this->search->search($searchString, $searchParams);

        $result = array();
        if (!empty($searchResult['Lectures']['matches'])) {
            $lectureRepo = $this->em->getRepository('ArmdLectureBundle:Lecture');
            $items = $lectureRepo->findBy(array('id' => array_keys($searchResult['Lectures']['matches'])));
            $result = array(
                'total' => $searchResult['Lectures']['total'],
                'items' => $items,
            );
        }

        return $result;
    }

    public function getStructuredRolesPersons(\Armd\LectureBundle\Entity\Lecture $lecture)
    {
        $res = array();
        foreach ($lecture->getRolesPersons() as $rp) {
            $res[$rp->getRole()->getName()][] = $rp->getPerson();
        }

        return $res;
    }

    public function getGenresBySupertype($superTypeId)
    {
        $qb = $this->em->getRepository('ArmdLectureBundle:LectureCategory')->createQueryBuilder('t');
        $qb->where('t.root = :superTypeId')
            ->setParameter('superTypeId', $superTypeId)
            ->andWhere('t.lvl > 1')
            ->orderBy('t.title', 'ASC');

        $rows = $qb->getQuery()->getResult();

        $res = array();
        foreach ($rows as $row) {
            $res[] = array(
                'title' => $row->getTitle(),
                'value' => $row->getId(),
            );
        }

        return $res;
    }


    public function getClassName()
    {
        return 'Armd\LectureBundle\Entity\Lecture';
    }

    public function getTaggableType()
    {
        return 'armd_lecture';
    }
}
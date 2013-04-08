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

    /** example: array('LECTURE_SUPER_TYPE_CINEMA', 'LECTURE_SUPER_TYPE_LECTURE') */
    const CRITERIA_SUPER_TYPE_CODES_OR = 'CRITERIA_SUPER_TYPE_CODES_OR';

    /** example: array(1, 2) */
    const CRITERIA_SUPER_TYPE_IDS_OR = 'CRITERIA_SUPER_TYPE_IDS_OR';

    /** example: array(1, 2) */
    const CRITERIA_CATEGORY_IDS_OR = 'CRITERIA_CATEGORY_IDS_OR';

    /** exampple: 12 */
    const CRITERIA_CATEGORY_ID_OR_PARENT_ID = 'CRITERIA_CATEGORY_ID_OR_PARENT_ID';

    /** example: 'the rolling stones' */
    const CRITERIA_SEARCH_STRING = 'CRITERIA_SEARCH_STRING';

    /** example: true */
    const CRITERIA_IS_TOP_100_FILM = 'CRITERIA_IS_TOP_100_FILM';

    /** example: 'A' */
    const CRITERIA_FIRST_LETTER = 'CRITERIA_FIRST_LETTER';

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

        if (!empty($criteria[self::CRITERIA_SUPER_TYPE_IDS_OR])) {
            $qb->andWhere('_lecture.lectureSuperType IN (:super_type_ids_or)')
                ->setParameter('super_type_ids_or', $criteria[self::CRITERIA_SUPER_TYPE_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_OR])) {
            $qb->innerJoin('_lecture.categories', '_lectureCategories')
                ->andWhere('_lectureCategories IN (:category_ids_or)')
                ->setParameter('category_ids_or', $criteria[self::CRITERIA_CATEGORY_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_CATEGORY_ID_OR_PARENT_ID])) {
            $category = $this->em->find(
                'ArmdLectureBundle:LectureCategory',
                $criteria[self::CRITERIA_CATEGORY_ID_OR_PARENT_ID]
            );

            $qb->innerJoin('_lecture.categories', '_lectureCategoryCP')
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->eq('_lectureCategoryCP', ':category'),
                    $qb->expr()->andX(
                        $qb->expr()->gt('_lectureCategoryCP.lft', ':parent_lft'),
                        $qb->expr()->lt('_lectureCategoryCP.rgt', ':parent_rgt'))
                    )
                )
                ->setParameter('category', $category)
                ->setParameter('parent_lft', $category->getLft())
                ->setParameter('parent_rgt', $category->getRgt());
        }

        if (!empty($criteria[self::CRITERIA_IS_TOP_100_FILM])) {
            $qb->andWhere('_lecture.isTop100Film = TRUE');
        }

        if (!empty($criteria[self::CRITERIA_FIRST_LETTER])) {
            $qb->andWhere("SUBSTRING(TRIM(LEADING ' .\"«' FROM _lecture.title), 1, 1) = :first_letter")
                ->setParameter('first_letter', $criteria[self::CRITERIA_FIRST_LETTER]);
        }
    }

    public function findObjectsWithSphinx($criteria) {
        $searchParams = array('Lectures' => array('filters' => array()));

        if (isset($criteria[self::CRITERIA_LIMIT])) {
            $searchParams['Lectures']['result_limit'] = (int) $criteria[self::CRITERIA_LIMIT];
        }

        if (isset($criteria[self::CRITERIA_OFFSET])) {
            $searchParams['Lectures']['result_offset'] = (int) $criteria[self::CRITERIA_OFFSET];
        }

        if (!empty($criteria[self::CRITERIA_SUPER_TYPE_IDS_OR])) {
            $searchParams['Lectures']['filters'][] = array(
                'attribute' => 'lecture_super_type_id',
                'values' => $criteria[self::CRITERIA_SUPER_TYPE_IDS_OR]
            );
        }

        if (!empty($criteria[self::CRITERIA_SUPER_TYPE_CODES_OR])) {
            $ids = array();
            foreach ($criteria[self::CRITERIA_SUPER_TYPE_CODES_OR] as $code) {
                $type = $this->em->getRepository('ArmdLectureBundle:LectureSuperType')->findOneByCode($code);
                if ($type) {
                    $ids[] = $type->getId();
                }
            }
            if (count($ids)) {
                $searchParams['Lectures']['filters'][] = array(
                    'attribute' => 'lecture_super_type_id',
                    'values' => $ids
                );
            }
        }

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_OR])) {
            $searchParams['Lectures']['filters'][] = array(
                'attribute' => 'lecturecategory_id',
                'values' => $criteria[self::CRITERIA_CATEGORY_IDS_OR]
            );
        }

        $searchResult = $this->search->search($criteria[self::CRITERIA_SEARCH_STRING], $searchParams);

        $result = array();
        if (!empty($searchResult['Lectures']['matches'])) {
            $lectureRepo = $this->em->getRepository('ArmdLectureBundle:Lecture');
            $ids = array_keys($searchResult['Lectures']['matches']);
            foreach ($ids as $id) {
                $entity = $lectureRepo->find($id);
                if (!empty($entity)) {
                    $result[] = $entity;
                }
            }
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

    public function getCategoriesBySuperType(LectureSuperType $superType, LectureCategory $parentCategory = null)
    {
        $qb = $this->em->getRepository('ArmdLectureBundle:LectureCategory')->createQueryBuilder('t');

        $qbRoot = clone($qb);
        $rootCategory = $qbRoot
            ->where('t.lectureSuperType = :superType')
            ->andWhere('t.lvl = 0')
            ->setParameter('superType', $superType)
            ->getQuery()
            ->getSingleResult();

        $qb->where('t.root = :root')
            ->setParameter('root', $rootCategory->getRoot())
            ->orderBy('t.title', 'ASC')
        ;

        if ($parentCategory) {
            $qb->andWhere('t.parent = :parent_category')
                ->andWhere('t.lvl = :category_level')
                ->setParameter('parent_category', $parentCategory)
                ->setParameter('category_level', $parentCategory->getLvl() + 1);
        } else {
            $qb->andWhere('t.lvl = 1');
        }

        $categories = $qb->getQuery()->getResult();

        return $categories;
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
<?php

namespace Armd\LectureBundle\Repository;

use \Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Armd\LectureBundle\Entity\LectureSuperType;
use Knp\Component\Pager\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

class LectureRepository extends EntityRepository
{

    public function findRecommended(LectureSuperType $superType, $limit = 4)
    {
        $qb = $this->createQueryBuilder('l')
            ->where('l.recommended = TRUE')
            ->andWhere('l.lectureSuperType = :superType')
            ->andWhere('l.published = TRUE')
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameters(
            array(
                'superType' => $superType
            )
        );

        $this->makeQueryBuilderEager($qb);
        $paginator = new DoctrinePaginator($qb->getQuery(), true);

        return $paginator->getIterator();
    }

    public function findLastAdded(LectureSuperType $superType = null, $limit = 2)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('l')
            ->andWhere('l.published = TRUE')
            ->orderBy('l.createdAt', 'DESC')
            ->setMaxResults($limit);

        if(!empty($superType)) {
            $qb->where('l.lectureSuperType = :superType')
                ->setParameters(array('superType' => $superType));

        }


        $this->makeQueryBuilderEager($qb);

        $paginator = new DoctrinePaginator($qb->getQuery(), true);
        return $paginator->getIterator();
    }


    public function findRelated(array $tags, $limit, LectureSuperType $superType)
    {
        // now they are just random
        $qb = $this->getFilterQueryBuilder('l', $superType, 'all', 'all', null);
        $qbCount = clone($qb);
        $count = $qbCount->select('COUNT(l)')->getQuery()->getSingleScalarResult();

        $lectures = array();
        if ($count) {
            $offsets = array();
            for ($i = 0; $i < $limit; $i++) {
                $j = 0;
                do {
                    $offset = rand(0, $count - 1);
                } while ($j++ < 10 && in_array($offset, $offsets));
                $offsets[] = $offset;
            }

            foreach ($offsets as $offset) {
                $lectures[] = $qb->setMaxResults(1)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getSingleResult();
            }
        }

        return $lectures;
    }

    public function getFilterQueryBuilder($alias, $superType, $typeIds = null, $categoryIds = null, $sortBy = 'date')
    {
        $qb = $this->createQueryBuilder($alias)
            ->innerJoin($alias . '.categories', 'c')
            ->where($alias . '.lectureSuperType = :superType')->setParameter('superType', $superType)
            ->andWhere('l.published = TRUE');

        // filter by types
        if ($typeIds !== 'all') {
            $qb->andWhere('l.lectureType IN (:lecture_type_ids)')
                ->setParameter('lecture_type_ids', $typeIds);
        }

        // filter by categories
        if ($categoryIds !== 'all') {
            $qb->andWhere('c IN (:category_ids)')
                ->setParameter('category_ids', $categoryIds);
        }

        // sort
        if ($sortBy === 'date') {
            $qb->orderBy('l.createdAt', 'DESC');
        } elseif ($sortBy === 'comments') {

        }

        $this->makeQueryBuilderEager($qb);

        return $qb;
    }

    public function getTypeCategories(LectureSuperType $superType)
    {
        $types = $this->_em->getRepository('ArmdLectureBundle:LectureType')
            ->findAll();

        $result = array();
        foreach ($types as $type) {
            $categories = $this->_em->createQueryBuilder()
                ->select('c')
                ->from('ArmdLectureBundle:LectureCategory', 'c')
                ->innerJoin('c.lectures', 'l')
                ->where('l.lectureType = :type')
                ->andWhere('l.lectureSuperType = :superType')
                ->andWhere('l.published = TRUE')
                ->groupBy('c')
                ->setParameters(
                array(
                    'type' => $type,
                    'superType' => $superType
                )
            )
                ->getQuery()->getResult();

            $result[$type->getId()] = array();
            foreach ($categories as $category) {
                $result[$type->getId()][] = $category->getId();
            }
        }

        return $result;
    }

    protected function makeQueryBuilderEager(QueryBuilder $qb)
    {
        $rootAliases = $qb->getRootAliases();
        $rootAlias = $rootAliases[0];

        // categories are removed because
        $qb
            ->addSelect('_categories')
            ->addSelect('_lecture_video')
            ->addSelect('_lecture_super_type')
            ->addSelect('_lecture_video_image_media')
            ->addSelect('_lecture_media_lecture_video')
            ->addSelect('_lecture_media_trailer_video')
            ->leftJoin($rootAlias . '.categories', '_categories')
            ->leftJoin($rootAlias . '.lectureVideo', '_lecture_video')
            ->leftJoin($rootAlias . '.lectureSuperType', '_lecture_super_type')
            ->leftJoin('_lecture_video.imageMedia', '_lecture_video_image_media')
            ->leftJoin($rootAlias . '.mediaLectureVideo', '_lecture_media_lecture_video')
            ->leftJoin($rootAlias . '.mediaTrailerVideo', '_lecture_media_trailer_video');
    }

    /**
     * @param string $date
     * @param int $limit
     * @param string $type
     * @return array
     */
    public function findCinemaForMainPage($date = '', $limit = 5, $type = 'recommend')
    {
        $dt = new \DateTime();
        $dt->setTime(0, 0, 0);
        if ($date != '') {
            $tmp = explode('-', $date);
            $dt->setDate($tmp[0], $tmp[1], $tmp[2]);
        }

        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.lectureSuperType', 'c', 'WITH', 'c.code = :category')
            ->setParameter('category', 'LECTURE_SUPER_TYPE_CINEMA')
            ->setMaxResults($limit);

        switch ($type) {
            case 'children':
                $qb->where('a.showOnMainAsForChildren = :show')
                    ->setParameter('show', true)
                    ->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->andX(
                                'a.showOnMainAsForChildrenTo IS NULL',
                                'a.showOnMainAsForChildrenFrom IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsForChildrenFrom <= :dt',
                                'a.showOnMainAsForChildrenTo IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsForChildrenFrom IS NULL',
                                'a.showOnMainAsForChildrenTo >= :dt'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsForChildrenFrom <= :dt',
                                'a.showOnMainAsForChildrenTo >= :dt'
                            )
                        )
                    )
                    ->setParameter('dt', $dt)
                    ->orderBy('a.showOnMainAsForChildrenOrd');
                break;
            case 'recommend':
            default:
                $qb->where('a.showOnMainAsRecommended = :show')
                    ->setParameter('show', true)
                    ->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedTo IS NULL',
                                'a.showOnMainAsRecommendedFrom IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedFrom <= :dt',
                                'a.showOnMainAsRecommendedTo IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedFrom IS NULL',
                                'a.showOnMainAsRecommendedTo >= :dt'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedFrom <= :dt',
                                'a.showOnMainAsRecommendedTo >= :dt'
                            )
                        )
                    )
                    ->setParameter('dt', $dt)
                    ->orderBy('a.showOnMainAsRecommendedOrd');

        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $date
     * @param string $type
     * @return int
     */
    public function countCinemaForMainPage($date = '', $type = 'recommend')
    {
        $dt = new \DateTime();
        $dt->setTime(0, 0, 0);
        if ($date != '') {
            $tmp = explode('-', $date);
            $dt->setDate($tmp[0], $tmp[1], $tmp[2]);
        }

        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->innerJoin('a.lectureSuperType', 'c', 'WITH', 'c.code = :category')
            ->setParameter('category', 'LECTURE_SUPER_TYPE_CINEMA');

        switch ($type) {
            case 'children':
                $qb->where('a.showOnMainAsForChildren = :show')
                    ->setParameter('show', true)
                    ->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->andX(
                                'a.showOnMainAsForChildrenTo IS NULL',
                                'a.showOnMainAsForChildrenFrom IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsForChildrenFrom <= :dt',
                                'a.showOnMainAsForChildrenTo IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsForChildrenFrom IS NULL',
                                'a.showOnMainAsForChildrenTo >= :dt'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsForChildrenFrom <= :dt',
                                'a.showOnMainAsForChildrenTo >= :dt'
                            )
                        )
                    )
                    ->setParameter('dt', $dt);
                break;
            case 'recommend':
            default:
                $qb->where('a.showOnMainAsRecommended = :show')
                    ->setParameter('show', true)
                    ->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedTo IS NULL',
                                'a.showOnMainAsRecommendedFrom IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedFrom <= :dt',
                                'a.showOnMainAsRecommendedTo IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedFrom IS NULL',
                                'a.showOnMainAsRecommendedTo >= :dt'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedFrom <= :dt',
                                'a.showOnMainAsRecommendedTo >= :dt'
                            )
                        )
                    )
                    ->setParameter('dt', $dt);

        }

        return (int)$qb->getQuery()->getSingleScalarResult();
    }

    public function findForMainPage($date = '', $limit = 5, $type = 'recommend') {
        $dt = new \DateTime();
        $dt->setTime(0, 0, 0);

        if ($date != '') {
            $tmp = explode('-', $date);
            $dt->setDate($tmp[0], $tmp[1], $tmp[2]);
        }

        $qb = $this->createQueryBuilder('a')
            ->innerJoin('a.lectureSuperType', 'c', 'WITH', 'c.code = :category')
            ->setParameter('category', 'LECTURE_SUPER_TYPE_LECTURE')
            ->setMaxResults($limit);

        switch ($type) {
            case 'popular':
                $repo = $this->_em->getRepository('\Armd\UserBundle\Entity\ViewedContent');
                $ids = $repo->getTopLectures($limit);

                return $this->findBy(array('id' => $ids));
                break;

            case 'recommend':
            default:
                $qb->where('a.showOnMainAsRecommended = :show')
                    ->setParameter('show', true)
                    ->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedTo IS NULL',
                                'a.showOnMainAsRecommendedFrom IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedFrom <= :dt',
                                'a.showOnMainAsRecommendedTo IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainAsRecommendedFrom IS NULL',
                                'a.showOnMainAsRecommendedTo >= :dt'
                            ),
                            $qb->expr()->andX('a.showOnMainAsRecommendedFrom <= :dt', 'a.showOnMainAsRecommendedTo >= :dt')
                        )
                    )
                    ->setParameter('dt', $dt)
                    ->orderBy('a.showOnMainAsRecommendedOrd');
        }

        return $qb->getQuery()->getResult();
    }
}
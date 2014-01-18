<?php
namespace Armd\PerfomanceBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PerfomanceRepository extends EntityRepository {
    public function findForMainPage($date = '', $limit = 5, $type = 'recommend') {
        $dt = new \DateTime();
        $dt->setTime(0, 0, 0);

        if ($date != '') {
            $tmp = explode('-', $date);
            $dt->setDate($tmp[0], $tmp[1], $tmp[2]);
        }

        $qb = $this->createQueryBuilder('a')
            ->setMaxResults($limit);

        switch ($type) {
            case 'popular':
                $repo = $this->_em->getRepository('\Armd\UserBundle\Entity\ViewedContent');
                $ids = $repo->getTopPerfomances($limit);

                $qb->andWhere($qb->expr()->in('a.id', $ids)); //TODO: order by in postgres
                $objectsTmp = $qb->getQuery()->getResult();

                $objects = array();
                foreach ($ids as $id) {
                    foreach ($objectsTmp as $obj) {
                        if($obj->getId() == $id) {
                            $objects[] = $obj;
                            break;
                        }
                    }
                }
                unset($objectsTmp);
                break;

            case 'recommend':
            default:
                $qb->where('a.showOnMain = :show')
                    ->setParameter('show', true)
                    ->andWhere(
                        $qb->expr()->orX(
                            $qb->expr()->andX(
                                'a.showOnMainTo IS NULL',
                                'a.showOnMainFrom IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainFrom <= :dt',
                                'a.showOnMainTo IS NULL'
                            ),
                            $qb->expr()->andX(
                                'a.showOnMainFrom IS NULL',
                                'a.showOnMainTo >= :dt'
                            ),
                            $qb->expr()->andX('a.showOnMainFrom <= :dt', 'a.showOnMainTo >= :dt')
                        )
                    )
                    ->setParameter('dt', $dt)
                    ->orderBy('a.showOnMainOrd');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return int
     */
    public function getPerformancesCount() {
        $qb = $this->createQueryBuilder('p');

        return $qb->select('count(p.id)')
            ->where($qb->expr()->eq('p.published', $qb->expr()->literal(true)))
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int $amount
     * @param array $excludeIds
     * @param int $selectedTheater
     * @param int $selectedGenre
     *
     * @return array
     */
    public function getRandomSet($amount = 1, $excludeIds = array(), $selectedTheater = 0, $selectedGenre = 0) { //TODO: too many parameters
        $randomSet = array();
        $qb = $this->createQueryBuilder('p');
        $excludeIds = array_map('intval', $excludeIds);
        $randomIds = $this->getRandomIds($amount, $excludeIds, $selectedTheater, $selectedGenre);

        if (count($randomIds) > 0) {
            $randomSet = $qb->select('p')
                ->where($qb->expr()->in('p.id', $randomIds))
                ->andWhere($qb->expr()->eq('p.published', $qb->expr()->literal(true)))
                ->getQuery()->getResult();
        }

        return $randomSet;
    }



    private function getRandomIds($amount = 1, $excludeIds = array(), $selectedTheater = 0, $selectedGenre = 0) {
        $randomIds = array();
        $allIds = $this->getAllIds($excludeIds, $selectedTheater, $selectedGenre);

        if (count($allIds) == 0) {
            return $randomIds;
        }

        $amount = (count($allIds) < $amount) ? count($allIds) : $amount;
        $randomKeys = array_rand($allIds, $amount);

        if (!is_array($randomKeys)) {
            $randomKeys = array();
        }

        foreach ($randomKeys as $key) {
            $randomIds[] = $allIds[$key];
        }

        shuffle($randomIds);

        return $randomIds;
    }

    private function getAllIds($excludeIds = array(), $selectedTheater = 0, $selectedGenre = 0) {
        $qb = $this->createQueryBuilder('p')
                   ->select('p.id');

        if (count($excludeIds) > 0) {
            $qb->andWhere($qb->expr()->notIn('p.id', $excludeIds));
        }

        if ($selectedTheater) {
            $qb->leftJoin('p.theater', 't')
               ->andWhere($qb->expr()->eq('t.id', $selectedTheater));
        }

        if ($selectedGenre) {
            $qb->leftJoin('p.ganres', 'g')
               ->andWhere($qb->expr()->eq('g.id', $selectedGenre));
        }

        $allIdsAssocArray = $qb->getQuery()->getResult();
        $allIds = array_map('current', $allIdsAssocArray);

        return $allIds;
    }
}
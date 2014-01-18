<?php
namespace Armd\TheaterBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TheaterRepository extends EntityRepository {
    /**
     * @return array
     */
    public function getAllTheatersOrderedByTitleAsc() {
        $qb = $this->createQueryBuilder('p');

        return $qb->select('p')
            ->where($qb->expr()->eq('p.published', $qb->expr()->literal(true)))
            ->orderBy('p.title', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param int $amount
     * @param array $excludeIds
     * @param int $cityId
     * @param int $categoryId
     *
     * @return array
     */
    public function getRandomSet($amount = 0, $excludeIds = array(), $cityId = 0, $categoryId = 0) { //TODO: too many parameters
        $randomSet = array();
        $qb = $this->createQueryBuilder('p');
        $excludeIds = array_map('intval', $excludeIds);

        $randomIds = $this->getRandomIds($amount, $excludeIds, $cityId, $categoryId);

        if (count($randomIds) > 0) {
            $randomSet = $qb->select('p')
                ->where($qb->expr()->in('p.id', $randomIds))
                ->andWhere($qb->expr()->eq('p.published', $qb->expr()->literal(true)))
                ->getQuery()->getResult();
        }

        return $randomSet;
    }



    private function getRandomIds($amount = 0, $excludeIds = array(), $cityId = 0, $categoryId = 0) {
        $randomIds = array();
        $allIds = $this->getAllIds($excludeIds, $cityId, $categoryId);

        if (count($allIds) == 0) {
            return $randomIds;
        }

        if (count($allIds) == 1) {
            return $allIds;
        }

        if ($amount == 0) { //no limit
            $randomKeys = array_rand($allIds, count($allIds));
        }
        else {
            $amount = (count($allIds) < $amount) ? count($allIds) : $amount;
            $randomKeys = array_rand($allIds, $amount);
        }

        if (!is_array($randomKeys)) {
            $randomKeys = array();
        }

        foreach ($randomKeys as $key) {
            $randomIds[] = $allIds[$key];
        }

        shuffle($randomIds);

        return $randomIds;
    }

    private function getAllIds($excludeIds = array(), $cityId = 0, $categoryId = 0) {
        $qb = $this->createQueryBuilder('p')
            ->select('p.id');

        if (count($excludeIds) > 0) {
            $qb->andWhere($qb->expr()->notIn('p.id', $excludeIds));
        }

        if ($cityId) {
            $qb->andWhere($qb->expr()->eq('p.city', $cityId));
        }

        if ($categoryId) {
            $qb->leftJoin('p.categories', 't')
               ->andWhere($qb->expr()->eq('t.id', $categoryId));
        }

        $allIdsAssocArray = $qb->getQuery()->getResult();
        $allIds = array_map('current', $allIdsAssocArray);

        return $allIds;
    }
}
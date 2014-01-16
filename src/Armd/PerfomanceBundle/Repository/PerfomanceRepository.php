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
     * @param int $amount
     * @param array $excludeIds
     *
     * @return array
     */
    public function getRandomSet($amount = 1, $excludeIds = array()) {
        $qb = $this->createQueryBuilder('p');
        $excludeIds = array_map('intval', $excludeIds);
        $randomIds = $this->getRandomIds($amount, $excludeIds);

        return $qb->select('p')->where($qb->expr()->in('p.id', $randomIds))->getQuery()->getResult();
    }



    private function getAllIds($excludeIds = array()) {
        $qb = $this->createQueryBuilder('p');
        if (count($excludeIds) > 0) {
            $qb->select('p.id')->where($qb->expr()->notIn('p.id', $excludeIds));
        }
        else {
            $qb->select('p.id');
        }
        $allIdsAssocArray = $qb->getQuery()->getResult();

        $gg = array_map('current', $allIdsAssocArray);

print_r($gg);
        return $gg;
    }

    private function getRandomIds($amount = 1, $excludeIds = array()) {
        $result = array_rand($this->getAllIds($excludeIds), (integer)$amount);
        shuffle($result);

        return $result;
    }
}
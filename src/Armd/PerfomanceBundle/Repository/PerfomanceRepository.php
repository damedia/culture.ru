<?php

namespace Armd\PerfomanceBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PerfomanceRepository extends EntityRepository {
    private $allIds = null;

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



    public function getRandomSet($amount = 1) {
        $qb = $this->createQueryBuilder('p');
        $randomIds = $this->getRandomIds($amount);

        return $qb->select('p')
            ->Where('p.id IN(:ids)')
            ->setParameter('ids', $randomIds)
            ->getQuery()
            ->getResult();
    }



    private function getAllIds() {
        if (is_array($this->allIds)) {
            return $this->allIds;
        }

        $queryBuilder = $this->createQueryBuilder('p');
        $allIdsAssocArray = $queryBuilder->select('p.id')->getQuery()->getResult();
        $this->allIds = array_map('current', $allIdsAssocArray);

        return $this->allIds;
    }

    private function getRandomIds($amount = 1) {
        $result = array_rand($this->getAllIds(), (integer)$amount);
        shuffle($result);

        return $result;
    }
}
<?php

namespace Armd\NewsBundle\Repository;

use Armd\ListBundle\Repository\BaseRepository;

class NewsRepository extends BaseRepository
{
    /**
     * @param \DateTime $date
     * @return NewsRepository
     */
    function setBeginDate(\DateTime $date)
    {
        $this->qb
            ->andWhere("{$this->alias}.date >= :from")
            ->setParameter('from', $date)
        ;

        return $this;
    }

    /**
     * @param \DateTime $date
     * @return NewsRepository
     */
    function setEndDate(\DateTime $date)
    {
        $this->qb
            ->andWhere("{$this->alias}.date <= :to")
            ->setParameter('to', $date)
        ;

        return $this;
    }

    function setMonthAndDay(\DateTime $date)
    {
        $this->qb
            ->andWhere("{$this->alias}.month = :month")
            ->andWhere("{$this->alias}.day = :day")
            ->setParameter('month', $date->format('m'))
            ->setParameter('day', $date->format('d'))
        ;

        return $this;
    }

    function setImportant($isImportant)
    {
        $this->qb
            ->andWhere("{$this->alias}.important = :important")
            ->setParameter('important', $isImportant ? 1 : 0)
        ;

        return $this;
    }

    function setCategories(array $categories)
    {
        $this->qb
            ->innerJoin("{$this->alias}.category", 'c', 'WITH', "c.slug in (:categories)")
            ->setParameter('categories', $categories)
        ;

        return $this;
    }

    function setFiltrableCategories()
    {
        $this->qb
            ->innerJoin("{$this->alias}.category", 'c', 'WITH', "c.filtrable = :filtrable")
            ->setParameter('filtrable', 1)
        ;

        return $this;
    }

    function setPublication()
    {
        $this->qb
            ->andWhere("{$this->alias}.published = :published")
            ->setParameter('published', 1)
        ;
        return $this;
    }

    /**
     * @param string $order
     * @return NewsRepository
     */
    function orderByDate($order = 'desc')
    {
        $this->qb->orderBy("{$this->alias}.date", $order);

        return $this;
    }

    /**
     * @param string $order
     * @return NewsRepository
     */
    function orderByPriority($order = 'asc')
    {
        $this->qb->orderBy("{$this->alias}.priority", $order);

        return $this;
    }

    /**
     * @return NewsRepository
     */
    function orderByRand()
    {
        $this->qb->orderBy("rand()");

        return $this;
    }

    public function getLastAnnounce() {
        $qb = $this->createQueryBuilder('p');

        return $qb->select('p')->setMaxResults(1)
                               ->where('p.published = :published')
                               ->setParameter('published', true)
                               ->andWhere('p.category = :category')
                               ->setParameter('category', 3) //3 - events (announces)
                               ->orderBy('p.newsDate', 'DESC')
                               ->getQuery()
                               ->getSingleResult();
    }

    public function findForMainPage($date, $limit = 5)
    {
        $dt = new \DateTime();
//        $dt->setTime(0, 0, 0);
//        if ($date != '') {
//            $tmp = explode('-', $date);
//            $dt->setDate($tmp[0], $tmp[1], $tmp[2]);
//        }

        $qb = $this->createQueryBuilder('p');
        return $qb->select('p')
            ->setMaxResults($limit)
            ->Where('p.published = TRUE')
            ->andWhere('p.publishFromDate <= :dt')
//            ->andWhere('p.showOnMain = TRUE')
//            ->andWhere(
//                $qb->expr()->orX(
//                    $qb->expr()->andX(
//                        'p.showOnMainTo IS NULL',
//                        'p.showOnMainFrom IS NULL'
//                    ),
//                    $qb->expr()->andX(
//                        'p.showOnMainFrom <= :dt',
//                        'p.showOnMainTo IS NULL'
//                    ),
//                    $qb->expr()->andX(
//                        'p.showOnMainFrom IS NULL',
//                        'p.showOnMainTo >= :dt'
//                    ),
//                    $qb->expr()->andX('p.showOnMainFrom <= :dt', 'p.showOnMainTo >= :dt')
//                )
//            )
            ->setParameter('dt', $dt)
//            ->orderBy('p.showOnMainOrd')
            ->orderBy('p.newsDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

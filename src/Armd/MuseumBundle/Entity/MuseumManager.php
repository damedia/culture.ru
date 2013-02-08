<?php

namespace Armd\MuseumBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Armd\SphinxSearchBundle\Services\Search\SphinxSearch;
use Knp\Component\Pager\Paginator;
use Armd\ListBundle\Entity\ListManager;

class MuseumManager extends ListManager
{
    /** example: array(13, 7) */
    const CRITERIA_CATEGORY_IDS_OR = 'CRITERIA_CATEGORY_IDS_OR';

    public function getQueryBuilder()
    {
        $qb = $this->em->getRepository('ArmdMuseumBundle:Museum')
            ->createQueryBuilder('_museum');

        $qb->innerJoin('_museum.category', '_museumCategory')
            ->leftJoin('_museum.image', '_museumImage', 'WITH', '_museumImage.enabled = TRUE')
            ->andWhere('_museum.published = TRUE')
            ->orderBy('_museum.title', 'DESC')
        ;

        return $qb;
    }

    public function setCriteria(QueryBuilder $qb, $criteria)
    {
        parent::setCriteria($qb, $criteria);

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_OR])) {
            $qb->andWhere('_museum.category IN (:category_ids_or)')
                ->setParameter('category_ids_or', $criteria[self::CRITERIA_CATEGORY_IDS_OR]);
        }
    }

    public function getClassName()
    {
        return 'Armd\MuseumBundle\Entity\Museum';
    }

    public function getTaggableType()
    {
        return 'armd_museum';
    }

}

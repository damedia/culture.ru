<?php

namespace Armd\AtlasBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * TouristClusterRepository
 */
class TouristClusterRepository extends EntityRepository
{

    public function getDataForFilter($ids=array())
    {
        $qb = $this->createQueryBuilder('t1')
            ->select('t1.id, t1.title, 0 as hasIcon')
            ;
        $rows = $qb->getQuery()->getResult();
        $result = array(
                  array("id" => 0,
                        "title" => "Туристические кластеры",
                        "tags" => $rows
                  ));
        return $result;
    }

}

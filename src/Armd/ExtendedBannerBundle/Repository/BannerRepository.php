<?php

namespace Armd\ExtendedBannerBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Banner repository class
 */
class BannerRepository extends EntityRepository
{
    /**
     * @param $typeCode
     * @return \Armd\ExtendedBannerBundle\Entity\BaseBanner[]
     */
    public function getBanners($typeCode)
    {
        return $this->createActiveBannersQueryBuilder('b', $typeCode)
            ->orderBy('b.id', 'ASC')
            ->getQuery()->getResult();
    }

    /**
     * @param $typeCode
     * @return \Armd\ExtendedBannerBundle\Entity\BaseBanner|null
     */
    public function getBanner($typeCode)
    {
        $resultBanner = null;
        $banners = $this->getBanners($typeCode);

        if (count($banners)) {
            $totalWeight = 0;
            foreach ($banners as $banner) {
                $totalWeight += $banner->getPriority();
            }

            $rand = rand(1, $totalWeight);
            $counter = 0;
            foreach ($banners as $banner) {
                $counter += $banner->getPriority();
                if ($counter >= $rand) {
                    $resultBanner = $banner;
                    break;
                }
            }
        }

        return $resultBanner;
    }

    /**
     * @param $typeCode
     * @return bool
     */
    public function hasActiveBanner($typeCode)
    {
        $count = $this->countActiveBanners($typeCode);
        return empty($count) ? false : true;
    }

    /**
     * @param $typeCode
     * @return mixed
     */
    public function countActiveBanners($typeCode)
    {
        $count = $this->createActiveBannersQueryBuilder('b', $typeCode)
            ->select('COUNT(b)')
            ->getQuery()->getSingleScalarResult();
        return $count;
    }

    /**
     * @param $alias
     * @param $typeCode
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createActiveBannersQueryBuilder($alias, $typeCode)
    {
        $qb = $this->createQueryBuilder($alias);
        $qb->innerJoin($alias . '.type', 't')
            ->where('t.code = :code')
            ->andWhere($alias . '.startDate <= :currentDate')
            ->andWhere($alias . '.endDate >= :currentDate')
            ->andWhere($qb->expr()->orX(
            "$alias.maxViews = 0",
            "$alias.maxViews >= $alias.viewCount"))
            ->setParameters(
            array(
                'code' => $typeCode,
                'currentDate' => new \DateTime()
            ));
        return $qb;
    }
}
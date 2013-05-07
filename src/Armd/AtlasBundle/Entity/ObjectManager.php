<?php

namespace Armd\AtlasBundle\Entity;

use Symfony\Component\DependencyInjection\Container;
use Armd\SphinxSearchBundle\Services\Search\SphinxSearch;
use Armd\AtlasBundle\Entity\Object;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Exception\NotAllAclsFoundException;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Armd\TagBundle\Entity\TagManager;
use Armd\ListBundle\Entity\ListManager;


class ObjectManager extends ListManager
{
    private $search;

    /** example: true */
    const CRITERIA_RUSSIA_IMAGES = 'CRITERIA_RUSSIA_IMAGES';

    /** example: array(1, 89) */
    const CRITERIA_CATEGORY_IDS_AND = 'CRITERIA_CATEGORY_IDS_AND';

    /** example: array(1, 89) */
    const CRITERIA_CATEGORY_IDS_OR = 'CRITERIA_CATEGORY_IDS_OR';

    /** example: array(1, 89) */
    const CRITERIA_REGION_IDS_AND = 'CRITERIA_REGION_IDS_AND';

    /** example: array(1, 89) */
    const CRITERIA_REGION_IDS_OR = 'CRITERIA_REGION_IDS_OR';

    /** example: true */
    const CRITERIA_HAS_SIDE_BANNER_IMAGE = 'CRITERIA_HAS_SIDE_BANNER_IMAGE';

    /** example: 'the rolling stones' */
    const CRITERIA_SEARCH_STRING = 'CRITERIA_SEARCH_STRING';


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

    /**
     * Get objects count.
     *
     * @param array $criteria
     * @return integer
     */
    public function findObjectsCount(array $criteria)
    {
        if (!empty($criteria[self::CRITERIA_SEARCH_STRING])) {
            return $this->findObjectsWithSphinxCount($criteria);

        } else {
            return parent::findObjectsCount($criteria);
        }
    }

    public function getQueryBuilder()
    {
        $qb = $this->em->getRepository('ArmdAtlasBundle:Object')->createQueryBuilder('o')
            ->where('o.published = TRUE');

        return $qb;
    }

    public function setCriteria(QueryBuilder $qb, $criteria)
    {
        parent::setCriteria($qb, $criteria);

        $aliases = $qb->getRootAliases();
        $o = $aliases[0];

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_OR])) {
            $qb->innerJoin("$o.secondaryCategories", 'sc')
                ->andWhere("sc IN (:categoryIds)")
                ->setParameter('categoryIds', $criteria[self::CRITERIA_CATEGORY_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_AND])) {
            foreach ($criteria[self::CRITERIA_CATEGORY_IDS_AND] as $i => $categoryId) {
                $qb->innerJoin("$o.secondaryCategories", 'sc'.$i);
                $parameterName = 'categoryId' . $i;
                $qb->andWhere("sc$i = :$parameterName")->setParameter($parameterName, $categoryId);
            }
        }

        if (!empty($criteria[self::CRITERIA_REGION_IDS_OR])) {
            $qb->innerJoin("$o.regions", 'r')
                ->andWhere("r IN (:regionIds)")
                ->setParameter('regionIds', $criteria[self::CRITERIA_REGION_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_REGION_IDS_AND])) {
            foreach ($criteria[self::CRITERIA_REGION_IDS_AND] as $i => $regionId) {
                $qb->innerJoin("$o.regions", 'r'.$i);
                $parameterName = 'regionId' . $i;
                $qb->andWhere("r$i = :$parameterName")->setParameter($parameterName, $regionId);
            }
        }

        if (!empty($criteria[self::CRITERIA_RUSSIA_IMAGES])) {
            $qb->andWhere("$o.showAtRussianImage = TRUE");
        }

        if (!empty($criteria[self::CRITERIA_HAS_SIDE_BANNER_IMAGE])) {
            $qb->andWhere("$o.sideBannerImage IS NOT NULL");
        }
    }

    public function getUserObjects(UserInterface $user)
    {
        $sid = UserSecurityIdentity::fromAccount($user);
        $sidIdentifier = $sid->getClass() . '-' . $sid->getUsername();


        $sql = "
            SELECT DISTINCT aoi.object_identifier
            FROM acl_object_identities aoi
                INNER JOIN acl_classes ac ON ac.id = aoi.class_id
                INNER JOIN acl_entries ae ON ae.object_identity_id = aoi.id
                INNER JOIN acl_security_identities asi ON asi.id = ae.security_identity_id
                INNER JOIN atlas_object ato ON ato.id = CAST(aoi.object_identifier as int)
            WHERE
                asi.identifier = :sid_identifier
                AND ac.class_type = :class_type
                AND ae.mask & :mask > 0

        ";
        $params = array(
            'sid_identifier' => $sidIdentifier,
            'class_type' => 'Armd\AtlasBundle\Entity\Object',
            'mask' => MaskBuilder::MASK_EDIT
        );

        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        if (!empty($rows)) {
            $objectIds = array();
            foreach ($rows as $row) {
                $objectIds[] = $row['object_identifier'];
            }

            $objects = $this->em->getRepository('ArmdAtlasBundle:Object')
                ->createQueryBuilder('o')
                ->where('o IN (:objectIds)')
                ->orderBy('o.title', 'ASC')
                ->setParameters(array('objectIds' => $objectIds))
                ->getQuery()->getResult();
        } else {
            $objects = array();
        }

        return $objects;
    }

    public function findObjectsWithSphinx($criteria) {
        $searchParams = array('Atlas' => array('filters' => array()));

        if (isset($criteria[self::CRITERIA_LIMIT])) {
            $searchParams['Atlas']['result_limit'] = (int) $criteria[self::CRITERIA_LIMIT];
        }

        if (isset($criteria[self::CRITERIA_OFFSET])) {
            $searchParams['Atlas']['result_offset'] = (int) $criteria[self::CRITERIA_OFFSET];
        }

        if (isset($criteria[self::CRITERIA_RUSSIA_IMAGES])) {
            $searchParams['Atlas']['filters'][] = array(
                'attribute' => 'show_at_russian_image',
                'values' => array(1)
            );
        }

        $searchResult = $this->search->search($criteria[self::CRITERIA_SEARCH_STRING], $searchParams);

        $result = array();
        if (!empty($searchResult['Atlas']['matches'])) {
            $lectureRepo = $this->em->getRepository('ArmdAtlasBundle:Object');
            $result = $lectureRepo->findBy(array('id' => array_keys($searchResult['Atlas']['matches'])));
        }

        return $result;
    }

    /**
     * Get objects count.
     *
     * @param array $criteria
     * @retirn integer
     */
    public function findObjectsWithSphinxCount(array $criteria)
    {
        $searchParams = array('Atlas' => array('filters' => array()));

        if (isset($criteria[self::CRITERIA_RUSSIA_IMAGES])) {
            $searchParams['Atlas']['filters'][] = array(
                'attribute' => 'show_at_russian_image',
                'values' => array(1)
            );
        }

        $searchResult = $this->search->search($criteria[self::CRITERIA_SEARCH_STRING], $searchParams);

        if (!empty($searchResult['Atlas']['matches'])) {
            $qb = $this->getQueryBuilder();
            list($alias) = $qb->getRootAliases();
            $qb->select('COUNT(' .$alias .'.id) as total');
            $qb->andWhere($qb->expr()->in($alias. '.id', array_keys($searchResult['Atlas']['matches'])));
            $total = $qb->getQuery()->getSingleResult();

            return $total['total'];
        }

        return 0;
    }

    public function getRussiaImagesList($searchString)
    {
        $objectRepo = $this->em->getRepository('ArmdAtlasBundle:Object');

        if (!empty($searchString)) {
            $searchParams = array(
                'Atlas' => array(
                    'filters' => array(
                        array(
                            'attribute' => 'show_at_russian_image',
                            'values' => array(1)
                        ),
                        array(
                            'attribute' => 'published',
                            'values' => array(1)
                        )
                    )
                )
            );

            $searchResult = $this->search->search($searchString, $searchParams);
            $objects = array();
            if (!empty($searchResult['Atlas']['matches'])) {
                foreach ($searchResult['Atlas']['matches'] as $id => $data) {
                    $object = $objectRepo->find($id);
                    if (!empty($object)) {
                        $objects[] = $object;
                    }
                }
            }

        } else {
            $objects = $objectRepo->findRussiaImages();
        }

        return $objects;
    }

    public function getRussiaImagesDistinctRegions()
    {
        $sql = 'SELECT DISTINCT ro.region_id id, r.title
                FROM atlas_object_region ro
                LEFT JOIN atlas_region r ON r.id=ro.region_id
                JOIN atlas_object o ON o.published=TRUE AND o.show_at_russian_image=TRUE AND ro.object_id = o.id
                ORDER BY title ASC';
        $stmt = $this->em->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getObject($id)
    {
        $repo = $this->em->getRepository('ArmdAtlasBundle:Object');
        $entity = $repo->findOneBy(
            array(
                'published' => true,
                'id' => $id,
            )
        );

        return $entity;
    }

    public function getPublishedObjects($ids, $limit = 10)
    {
        $repo = $this->em->getRepository('ArmdAtlasBundle:Object');
        $entities = $repo->findBy(
            array(
                'published' => true,
                'id' => $ids,
            ),
            array('id' => 'ASC'),
            $limit
        );

        return $entities;
    }

    public function updateImageDescription($atlasObject)
    {
        if ($atlasObject->getPrimaryImage() && !$atlasObject->getPrimaryImage()->getDescription()) {
            $atlasObject->getPrimaryImage()->setDescription($atlasObject->getTitle());
        }
    }

    /**
     * Возвращает список id объектов и удаленность от заданной точки
     */
    public function findNearestRussianImages($object, $limit = 100, $radius = 10000000)
    {
        $latitude = $object->getLat();
        $longitude = $object->getLon();
        $circle = (float)$radius * 1.61;
        $limit++; // потому что исключаем исходный объект

        $cl = $this->search->getSphinx();
        $cl->setGeoAnchor('rad_lat', 'rad_lon', (float)deg2rad($latitude), (float)deg2rad($longitude));
        $cl->setFilterFloatRange('@geodist', 0.0, $circle);
        $cl->setFilter('show_at_russian_image', array(1));
        $cl->setFilter('published', array(1));
        $cl->setSortMode(SPH_SORT_EXTENDED, '@geodist ASC');
        $cl->setArrayResult(true);
        $cl->setLimits(0, $limit);

        // ищем в индексе объекты в радиусе от точки. сортировка по удаленности
        $result = $cl->query('', 'mk_atlas');

        // обработка результатов запроса
        if ($result === false) {
            throw new \Exception("Sphinx query failed: " . $cl->getLastError());
        }

        if ($cl->getLastWarning()) {
            throw new \Exception("Sphinx query warning: " . $cl->getLastWarning());
        }

        // если есть результаты поиска - обрабатываем их
        if (!empty($result["matches"])) {
            $objectIds = array();
            foreach ($result['matches'] as $i => $m) {
                if ($i) {
                    $objectIds[] = array(
                        'id' => $m['id'],
                        'distance' => $m['attrs']['@geodist'],
                    );
                }
            }

            return $objectIds;
        } else {
            return false;
        }
    }

    public function getClassName()
    {
        return 'Armd\AtlasBundle\Entity\Object';
    }

    public function getTaggableType()
    {
        return 'armd_atlas_object';
    }
}

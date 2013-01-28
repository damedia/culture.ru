<?php

namespace Armd\AtlasBundle\Manager;

use Symfony\Component\DependencyInjection\Container;
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

    private $tagManager;

    /** example: 10 */
    const CRITERIA_LIMIT = 'CRITERIA_LIMIT';

    /** example: 100  */
    const CRITERIA_OFFSET = 'CRITERIA_OFFSET';

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

    /** example: array('title' => 'ASC', 'createdAt' => 'DESC') */
    const CRITERIA_ORDER_BY = 'CRITERIA_ORDER_BY';

    /** example: 5 */
    const CRITERIA_RANDOM = 'CRITERIA_RANDOM';

    /** example: true */
    const CRITERIA_HAS_SIDE_BANNER_IMAGE = 'CRITERIA_HAS_SIDE_BANNER_IMAGE';

    /** example: 'the rolling stones' */
    const CRITERIA_SEARCH_STRING = 'CRITERIA_SEARCH_STRING';


    /** example: array('museum', 'world war') */
    const CRITERIA_TAGS = 'CRITERIA_TAGS';

    public function __construct(EntityManager $em, $search, TagManager $tagManager)
    {
        $this->em = $em;
        $this->search = $search;
        $this->tagManager = $tagManager;
    }

    public function findObjects(array $criteria)
    {

        if (!empty($criteria[self::CRITERIA_RANDOM])) {
            $criteriaMod = $criteria;
            unset($criteriaMod[self::CRITERIA_LIMIT]);
            $qb = $this->getQueryBuilder($criteriaMod);
            $objects = $this->getRandomObjectsFromQueryBuilder($qb, $criteria[self::CRITERIA_RANDOM]);

        } elseif (!empty($criteria[self::CRITERIA_TAGS])) {
            if (empty($criteria[self::CRITERIA_LIMIT])) {
                throw new \LogicException('Criteria ObjectManager::CRITERIA_LIMIT must specified when searching with ObjectManager::CRITERIA_TAGS');
            }
            $objects = $this->getTaggedObjects($criteria[self::CRITERIA_TAGS], $criteria[self::CRITERIA_LIMIT]);
            if (count($objects) < $criteria[self::CRITERIA_LIMIT]) {
                $criteria[self::CRITERIA_RANDOM] = $criteria[self::CRITERIA_LIMIT] - count($objects);
                $paddingObjects = $this->findObjects($criteria);
                $objects = array_merge($objects, $paddingObjects);
            }

        } else {
            $qb = $this->getQueryBuilder($criteria);
            $objects = $qb->getQuery()->getResult();
        }

        return $objects;
    }


    public function getQueryBuilder(array $criteria)
    {
        $qb = $this->em->getRepository('ArmdAtlasBundle:Object')->createQueryBuilder('o')
            ->where('o.published = TRUE');

        $this->setCriteria($qb, $criteria);

        return $qb;
    }

    public function setCriteria(QueryBuilder $qb, $criteria)
    {
        $aliases = $qb->getRootAliases();
        $o = $aliases[0];


        if (!empty($criteria[self::CRITERIA_OFFSET])) {
            $qb->setFirstResult($criteria[self::CRITERIA_OFFSET]);
        }

        if (!empty($criteria[self::CRITERIA_LIMIT])) {
            $qb->setMaxResults($criteria[self::CRITERIA_LIMIT]);
        }

        if (!empty($criteria[self::CRITERIA_ORDER_BY])) {
            foreach ($criteria[self::CRITERIA_ORDER_BY] as $k => $v) {
                $qb->addOrderBy("$o.$k", $v);
            }
        }

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_OR])) {
            $qb->innerJoin("$o.secondaryCategories", 'sc')
                ->andWhere("sc IN (:categoryIds)")
                ->setParameter('categoryIds', $criteria[self::CRITERIA_CATEGORY_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_AND])) {
            $qb->innerJoin("$o.secondaryCategories", 'sc');
            $i = 0;
            foreach($criteria[self::CRITERIA_CATEGORY_IDS_AND] as $categoryId) {
                $parameterName = 'categoryId' . $i;
                $qb->andWhere("sc = :$parameterName")->setParameter($parameterName, $categoryId);
                $i++;
            }
        }

        if (!empty($criteria[self::CRITERIA_REGION_IDS_OR])) {
            $qb->innerJoin("$o.regions", 'r')
                ->andWhere("r IN (:regionIds)")
                ->setParameter('regionIds', $criteria[self::CRITERIA_REGION_IDS_OR]);
        }

        if (!empty($criteria[self::CRITERIA_REGION_IDS_AND])) {
            $qb->innerJoin("$o.regions", 'r');
            $i = 0;
            foreach($criteria[self::CRITERIA_REGION_IDS_AND] as $regionId) {
                $parameterName = 'regionId' . $i;
                $qb->andWhere("r = :$parameterName")->setParameter($parameterName, $regionId);
                $i++;
            }
        }

        if (!empty($criteria[self::CRITERIA_RUSSIA_IMAGES])) {
            $qb->andWhere("$o.showAtRussianImage = TRUE");
        }

        if (!empty($criteria[self::CRITERIA_HAS_SIDE_BANNER_IMAGE])) {
            $qb->andWhere("$o.sideBannerImage IS NOT NULL");
        }


    }

    public function getRandomObjectsFromQueryBuilder(QueryBuilder $qb, $limit) {
        $qbCount = clone($qb);
        $aliases = $qbCount->getRootAliases();
        $o = $aliases[0];
        $objectCount = $qbCount->select("COUNT($o)")->getQuery()->getSingleScalarResult();

        if ($objectCount <= $limit) {
            $objects = $qb->getQuery()->getResult();
        }
        else {
            $offsets = array();
            for ($i = 0; $i < $limit; $i++) {
                $j = 0;
                do {
                    $offset = rand(0, $objectCount - 1);
                } while ($j++ < 10 && in_array($offset, $offsets));
                $offsets[] = $offset;
            }

            $objects = array();
            foreach ($offsets as $offset) {
                $objects[] = $qb->setMaxResults(1)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getSingleResult();
            }
        }
        return $objects;
    }

    public function getTaggedObjects($tags, $limit) {
        $resourceIds = $this->tagManager->getResourceIdsByTags('armd_atlas_object', $tags, $limit);
        $resourceIds = array_slice($resourceIds, 0, $limit);
        $objects = $this->em->createQueryBuilder()
            ->select('o')
            ->from('ArmdAtlasBundle:Object', 'o')
            ->where('o.id IN (:ids)')->setParameter('ids', $resourceIds)
            ->getQuery()
            ->getResult();

        return $objects;
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


}

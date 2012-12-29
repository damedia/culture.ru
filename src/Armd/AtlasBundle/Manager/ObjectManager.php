<?php

namespace Armd\AtlasBundle\Manager;

use Symfony\Component\DependencyInjection\Container;
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


class ObjectManager
{
    private $em;

    private $search;

    /** example: 10 */
    const CRITERIA_LIMIT = 'CRITERIA_LIMIT';

    /** example: 100  */
    const CRITERIA_OFFSET = 'CRITERIA_OFFSET';

    /** example: true */
    const CRITERIA_RUSSIA_IMAGES = 'CRITERIA_RUSSIA_IMAGES';

    /** example: array(1, 89) */
    const CRITERIA_CATEGORY_IDS_AND = 'CRITERIA_CATEGORY_IDS_AND';

    /** example: array(1, 89) */
    const CRITERIA_REGION_IDS_AND = 'CRITERIA_REGION_IDS_AND'; // array(4, 77)

    /** example: array('title' => 'ASC', 'createdAt' => 'DESC') */
    const CRITERIA_ORDER_BY = 'CRITERIA_ORDER_BY';

//    /** example: true */
//    const CRITERIA_LAST_CREATED = 'CRITERIA_LAST_CREATED';

    /** example: 5 */
    const CRITERIA_RANDOM = 'CRITERIA_RANDOM';

    /** example: 'the rolling stones' */
    const CRITERIA_SEARCH_STRING = 'CRITERIA_SEARCH_STRING';

    public function __construct(EntityManager $em, $search)
    {
        $this->em = $em;
        $this->search = $search;
    }

    public function findObjects(array $criteria)
    {
        $qb = $this->getQueryBuilder($criteria);
        $objects = $qb->getQuery()->getResult();

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

        if (!empty($criteria[self::CRITERIA_CATEGORY_IDS_AND])) {
            $qb->innerJoin("$o.secondaryCategories", 'sc')
                ->andWhere("sc IN (:categoryIds)")
                ->setParameter('categoryIds', $criteria[self::CRITERIA_CATEGORY_IDS_AND]);
        }

        if (!empty($criteria[self::CRITERIA_REGION_IDS_AND])) {
            $qb->innerJoin("$o.regions", 'r')
                ->andWhere("r IN (:regionIds)")
                ->setParameter('regionIds', $criteria[self::CRITERIA_REGION_IDS_AND]);
        }

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

        if (!empty($criteria[self::CRITERIA_RUSSIA_IMAGES])) {
            $qb->andWhere("$o.showAtRussianImage = TRUE");
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

        //--- following block may be used later if we'll decide to use objects for ACL read instead of raw SQL

        /*
        $objects = $this->om->getRepository('ArmdAtlasBundle:Object')->findAll();
        $oids = array();
        $oidToObject = array();
        foreach($objects as $key => $object) {
            $oid = ObjectIdentity::fromDomainObject($object);
            $oids[$key] = $oid;
            $oidToObject[$oid->getIdentifier()] = $key;
        }

        var_dump($oidToObject);


        $acls = $this->aclProvider->findAcls($oids, array($sid));
        foreach($acls as $oid) {
            if(!$acls->offsetGet($oid)->isGranted(array(MaskBuilder::CODE_EDIT), array($sid))) {
                unset($objects[$oidToObject[$oid->getIdentifier()]]);
            }
        }

        echo count($object);
        */

        //---

        //return $objects;
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

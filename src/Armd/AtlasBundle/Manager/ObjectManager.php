<?php

namespace Armd\AtlasBundle\Manager;

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
    // mapping field name to field getter
    public static $allowedFields = array(
        'id'       => 'getId',
        'title'    => 'getTitle',
        'announce' => 'getAnnounce',
        'lat'      => 'getLat',
        'lon'      => 'getLon',
        'icon'     => 'extractIconUrl', // this method
        'image'    => 'extractImageUrl', // this method
        'images'   => 'extractImagesUrls', // this method
    );

    private $em;
    
    private $search;

    private $container;

    public function __construct(EntityManager $em, $search, $container)
    {
        $this->em = $em;
        $this->search = $search;
        $this->container = $container;
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
                    if(!empty($object)) {
                        $objects[] = $object;
                    }
                }
            }

        }
        else {
            $objects = $objectRepo->findRussiaImages();
        }
        
        return $objects;        
    }
    
    public function getObject($id)
    {
        $repo =  $this->em->getRepository('ArmdAtlasBundle:Object');
        $entity = $repo->findOneBy(array(
            'published' => true,
            'id' => $id,
        ));

        return $entity;       
    }

    public function filterForApi($params=array())
    {
        $repo = $this->em->getRepository('ArmdAtlasBundle:Object');

        $qb = $repo->createQueryBuilder('o');
        $qb->where('o.published = TRUE');

        if (isset($params['id']) && is_array($params['id'])) {
            $ids = array_map('intval', $params['id']);
            $qb->andWhere('o.id IN (:ids)')
               ->setParameter('ids', $ids);
        }

        $rows = $qb->getQuery()->getResult();
        if (! $rows)
            throw new \Exception('Objects not found');

        if (isset($params['fields']) && is_array($params['fields'])) {
            $fields = $params['fields'];
        } else {
            $fields = array_keys(self::$allowedFields);
        }

        $res = array();
        foreach ($rows as $row) {
            $obj = array();
            foreach ($fields as $field) {
                if (in_array($field, $fields) && !empty($field)) {
                    $getterMethod = self::$allowedFields[$field];
                    $value = false;
                    if (method_exists($row, $getterMethod)) {
                        $value = $row->$getterMethod();
                    } elseif (method_exists($this, $getterMethod)) {
                        $value = $this->$getterMethod($row);
                    }
                    $obj[$field] = $value;
                }
            }
            $res[] = $obj;
        }

        return $res;
    }

    protected function extractIconUrl($entity)
    {
        $imageUrl = '';
        if ($entity->getPrimaryCategory()) {
            $image = $entity->getPrimaryCategory()->getIconMedia();
            $imageUrl = $this->container->get('sonata.media.twig.extension')->path($image, 'reference');
        }
        return $imageUrl;
    }

    protected function extractImageUrl($entity)
    {
        $imageUrl = $this->container->get('sonata.media.twig.extension')->path($entity->getPrimaryImage(), 'thumbnail');
        return $imageUrl;
    }

    protected function extractImagesUrls($entity)
    {
        $imagesUrls = array();
        $images = $entity->getImages();
        if ($images) {
            foreach ($images as $image) {
                $imagesUrls[] = $this->container->get('sonata.media.twig.extension')->path($entity->getPrimaryImage(), 'thumbnail');
            }
        }
        return $imagesUrls;
    }

}

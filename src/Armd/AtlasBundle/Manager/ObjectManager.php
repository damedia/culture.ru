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
    private $em;
    
    private $search;

    public function __construct(EntityManager $em, $search)
    {
        $this->em = $em;
        $this->search = $search;
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
}

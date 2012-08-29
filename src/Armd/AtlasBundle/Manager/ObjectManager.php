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
    private $securityContext;
    private $aclProvider;
    private $em;

    public function __construct(
        EntityManager $em,
        SecurityContextInterface $securityContext,
        AclProviderInterface $aclProvider
    )
    {
        $this->em = $em;
        $this->securityContext = $securityContext;
        $this->aclProvider = $aclProvider;
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
            WHERE
                asi.identifier = :sid_identifier
                AND ac.class_type = :class_type
                AND ae.mask & :mask

        ";


        $stmt = $this->em->getConnection()->prepare($sql);
//        $stmt->bindValue('sid_identifier', $sidIdentifier);
//        $stmt->bindValue('class_type', 'Armd\AtlasBundle\Entity\Object');
//        $stmt->bindValue('mask', MaskBuilder::MASK_EDIT);

        $stmt
            ->execute(array(
                'sid_identifier' => $sidIdentifier,
                'class_type' => $sidIdentifier,
                'mask' => MaskBuilder::MASK_EDIT
            ))
            ->fetchAll();
        $rows = $stmt->fetchAll();

        var_dump($rows);


        //--- following block may be used later if we'll decide to use objects for ACL read

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
}
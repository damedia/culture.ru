<?php

namespace Armd\AtlasBundle\DataFixtures\ORM\Test;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

class LoadObjectAclData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

    private $container;

    function load(ObjectManager $manager)
    {
        $user = $this->getReference('armd_user.user.expert1');

        $object = $this->getReference('armd_atlas.object.object1');
        $this->grant($user, $object);

        $object = $this->getReference('armd_atlas.object.object2');
        $this->grant($user, $object);

    }

    function grant($user, $object) {
        $aclProvider = $this->container->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $maskBuilder = new MaskBuilder();
        $maskBuilder->add('view')->add('edit');

        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
        }
        $acl->insertObjectAce($securityIdentity, $maskBuilder->get());
        $aclProvider->updateAcl($acl);
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    function getOrder()
    {
        return 140;
    }


}
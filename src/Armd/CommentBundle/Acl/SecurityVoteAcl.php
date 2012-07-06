<?php

namespace Armd\CommentBundle\Acl;

use FOS\CommentBundle\Acl\SecurityVoteAcl as BaseSecurityVoteAcl;

use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;

/**
 * Implements ACL checking using the Symfony2 Security component
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SecurityVoteAcl extends BaseSecurityVoteAcl
{
    protected function doInstallFallbackAcl(AclInterface $acl, MaskBuilder $builder)
    {
        $builder->add('iddqd');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_SUPER_ADMIN'), $builder->get());

        $builder->reset();
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'), $builder->get());

        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_USER'), $builder->get());
    }
}

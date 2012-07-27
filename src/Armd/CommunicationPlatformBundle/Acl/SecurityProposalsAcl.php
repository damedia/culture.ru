<?php

namespace Armd\CommunicationPlatformBundle\Acl;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;

use Armd\CommunicationPlatformBundle\Entity\Proposals;

class SecurityProposalsAcl
{
    /**
     * The AclProvider.
     *
     * @var MutableAclProviderInterface
     */
    protected $aclProvider;

    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * The FQCN of the Comment object.
     *
     * @var string
     */
    protected $commentClass;

    /**
     * The Class OID for the Comment object.
     *
     * @var ObjectIdentity
     */
    protected $oid;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface                 $securityContext
     * @param MutableAclProviderInterface              $aclProvider
     * @param string                                   $commentClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                MutableAclProviderInterface $aclProvider,
                                $commentClass
    )
    {
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->commentClass      = $commentClass;
        $this->oid               = new ObjectIdentity('class', $this->commentClass);
    }

    /**
     * @param Proposals $entity
     */
    public function setDefaultAcl(Proposals $entity)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        $user = $this->securityContext->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Checks if the Security token is allowed to create a new Comment.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Comment.
     *
     * @param  Proposals $comment
     * @return boolean
     */
    public function canView(Proposals $comment)
    {
        return $this->securityContext->isGranted('VIEW', $comment);
    }

    /**
     * Checks if the Security token is allowed to edit the specified Comment.
     *
     * @param  Proposals $comment
     * @return boolean
     */
    public function canEdit(Proposals $comment)
    {
        return $this->securityContext->isGranted('EDIT', $comment);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Comment.
     *
     * @param  Proposals $comment
     * @return boolean
     */
    public function canDelete(Proposals $comment)
    {
        return $this->securityContext->isGranted('DELETE', $comment);
    }
}
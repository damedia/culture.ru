<?php

namespace Armd\CommunicationPlatformBundle\Twig;

use Armd\CommunicationPlatformBundle\Acl\SecurityProposalsAcl;
use Armd\CommunicationPlatformBundle\Entity\Proposals;

class CommunicationPlatformExtension extends \Twig_Extension
{
    protected $proposalsAcl;

    /**
     * @param \Armd\CommunicationPlatformBundle\Acl\SecurityProposalsAcl|null $proposalsAcl
     */
    public function __construct(SecurityProposalsAcl $proposalsAcl = null)
    {
        $this->proposalsAcl = $proposalsAcl;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'can_delete_proposal' => new \Twig_Function_Method($this, 'canDeleteProposals'),
            'can_edit_proposal'   => new \Twig_Function_Method($this, 'canEditProposals'),
            'can_view_proposal'   => new \Twig_Function_Method($this, 'canViewProposals'),
            'can_create_proposal' => new \Twig_Function_Method($this, 'canCreateProposals'),
        );
    }

    /**
     * @param Proposals $proposal
     *
     * @return bool
     */
    public function canDeleteProposals(Proposals $proposal)
    {
        if (null === $this->proposalsAcl) {
            return true;
        }

        return $this->proposalsAcl->canDelete($proposal);
    }

    /**
     * @param Proposals $proposal
     *
     * @return bool
     */
    public function canEditProposals(Proposals $proposal)
    {
        if (null === $this->proposalsAcl) {
            return true;
        }

        return $this->proposalsAcl->canEdit($proposal);
    }

    /**
     * @param Proposals $proposal
     *
     * @return bool
     */
    public function canViewProposals(Proposals $proposal)
    {
        if (null === $this->proposalsAcl) {
            return true;
        }

        return $this->proposalsAcl->canView($proposal);
    }

    /**
     * @return bool
     */
    public function canCreateProposals()
    {
        if (null === $this->proposalsAcl) {
            return true;
        }

        return $this->proposalsAcl->canCreate();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'armd_cp';
    }
}
<?php

namespace Armd\UserBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Armd\UserBundle\Entity\User;

use DateTime;

class ActivityListener
{
    protected $context;
    protected $em;

    public function __construct(SecurityContext $context, Doctrine $doctrine)
    {
        $this->context = $context;
        $this->em = $doctrine->getEntityManager();
    }
    /**
     * On each request we want to update the user's last activity datetime
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     * @return void
     */
    public function onCoreController(FilterControllerEvent $event)
    {
        if ($this->context->getToken()) {
            $user = $this->context->getToken()->getUser();

            if($user instanceof User)
            {
                $user->setLastActivity(new DateTime());
                $this->em->persist($user);
                $this->em->flush($user);
            }
        }
    }
}
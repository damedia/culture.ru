<?php
namespace Armd\MainBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\SecurityContext;

class CacheListener
{
    protected $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $response->headers->set('X-MK-LOGGED-IN', 1);
        }
    }
}

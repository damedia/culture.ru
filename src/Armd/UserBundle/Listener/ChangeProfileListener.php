<?php

namespace Armd\UserBundle\Listener;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ChangeProfileListener implements EventSubscriberInterface
{

    private $mailer;
    private $tokenGenerator;
    private $router;
    private $session;
    private $security;
    private $email;

    public function __construct(MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator,
       UrlGeneratorInterface $router, SessionInterface $session, SecurityContextInterface $security)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->session = $session;
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return array(
                FOSUserEvents::PROFILE_EDIT_INITIALIZE => 'onProfileEditInitialize',
                FOSUserEvents::PROFILE_EDIT_SUCCESS    => 'onProfileEditSuccess',
        );
    }

    public function onProfileEditInitialize(GetResponseUserEvent $event)
    {
        // required, because when Success's event is called, session already contains new email
        $this->email = $this->security->getToken()->getUser()->getEmail();
    }

    public function onProfileEditSuccess(FormEvent $event)
    {
        $user = $event->getForm()->getData();

        if ($user->getEmail() !== $this->email)
        {
            // disable user
            $user->setEnabled(false);

            // send confirmation token to new email
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
            $this->mailer->sendProfileConfirmationEmailMessage($user);

            // force user to log-out
            $this->security->setToken();

            // redirect user to check email page
            $this->session->set('fos_user_send_confirmation_email/email', $user->getEmail());
            $url = $this->router->generate('armd_user_profile_check_email');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}

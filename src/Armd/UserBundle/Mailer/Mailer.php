<?php

namespace Armd\UserBundle\Mailer;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use FOS\UserBundle\Model\UserInterface;

use FOS\UserBundle\Mailer\Mailer as BaseMailer;

class Mailer extends BaseMailer
{
    public function sendProfileConfirmationEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['profile_confirmation.template'];
        $url = $this->router->generate('armd_user_profile_confirm', array('token' => $user->getConfirmationToken()), true);
        $rendered = $this->templating->render($template, array(
            'user' => $user,
            'confirmationUrl' =>  $url
        ));
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['profile_confirmation'], $user->getEmail());
    }
}

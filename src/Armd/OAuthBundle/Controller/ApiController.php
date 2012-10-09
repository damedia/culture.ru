<?php

namespace Armd\OAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Armd\UserBundle\Entity\User;

class ApiController extends Controller
{
    /**
     * @Route("/api/user-data")
     */
    public function userDataAction()
    {
        /** @var $user User */
        $user = $this->getUser();
        $userData = array(
            'username' => $user->getUsername(),
            'email' => $user->getEMail(),
            'first_name' => $user->getFirstname(),
            'last_name' => $user->getLastname(),
            'middle_name' => $user->getMiddlename(),
            'facebook_uid' => $user->getFacebookUid(),
            'vkontakte_uid' => $user->getVkontakteUid(),
            'twitter_uid' => $user->getTwitterUid(),
            'date_of_birth' => $user->getDateOfBirth(),
            'phone' => $user->getPhone()
        );

        return new Response(json_encode($userData));
    }

//    public function

}

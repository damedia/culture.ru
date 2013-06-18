<?php

namespace Armd\MkCommentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\UserBundle\Model\UserInterface;

class DefaultController extends Controller
{
    /**
     * 
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }
    
    /**
     * @Route("profile/notices/clear/", name="armd_comment_delete_notices")
     * @throws AccessDeniedException
     */
    public function deleleNoticesAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $this->container->get('fos_comment.manager.comment')->deleteUserNotices($user);
        
        return new RedirectResponse($this->generateUrl('armd_user_comments_notice'));
    }
}

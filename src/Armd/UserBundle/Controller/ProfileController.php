<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Armd\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\FOSUserEvents;

/**
 * This class is inspirated from the FOS Profile Controller, except :
 *   - only twig is supported
 *   - separation of the user authentication form with the profile form
 *
 */
class ProfileController extends Controller
{

    /**
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function showAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('ArmdUserBundle:Profile:show.html.twig', array(
            'user' => $user
        ));
    }

    /**
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function editAuthenticationAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $formFactory = $this->container->get('sonata_user_authentication_form_factory');
        $form = $formFactory->createForm();
        $form->setData($user);

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $form->bind($request);

             if ($form->isValid()) {
                $userManager = $this->container->get('fos_user.user_manager');
                $dispatcher = $this->container->get('event_dispatcher');

                $event = new FormEvent($form, $request);
                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

                $userManager->updateUser($user);
                $this->setFlash('fos_user_success', 'profile.flash.updated');

                if (null === $response = $event->getResponse()) {
                    $response = new RedirectResponse($this->generateUrl('sonata_user_profile_show'));
                }

                $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

                return $response;
            }
        }

        return $this->render('ArmdUserBundle:Profile:edit_authentication' .($this->checkXmlHttpRequest() ? '_form' : '') .'.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function editProfileAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $form = $this->container->get('sonata.user.profile.form');
        $formHandler = $this->container->get('sonata.user.profile.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->setFlash('fos_user_success', 'profile.flash.updated');

            return new RedirectResponse($this->generateUrl('sonata_user_profile_show'));
        }

        return $this->render('ArmdUserBundle:Profile:edit_profile' .($this->checkXmlHttpRequest() ? '_form' : '') .'.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function listCommentsAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $comments = $this->container->get('fos_comment.manager.comment')->findCommentsByUser($user);
        
        return $this->render('ArmdUserBundle:Profile:list_comments.html.twig', array(
            'entities' => $comments,
        ));
    }
    
    /**
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function listNoticesAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        
        $notices = $this->container->get('fos_comment.manager.comment')->findNoticesForUser($user);
        
        return $this->render('ArmdUserBundle:Profile:list_notices.html.twig', array(
            'entities' => $notices,
        ));
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->container->get('session')->setFlash($action, $value);
    }

    /**
     * @return bool
     */
    protected function checkXmlHttpRequest()
    {
        $request = $this->getRequest();
        
        return $request->isXmlHttpRequest() || $request->get('_xml_http_request', false);
    }
}

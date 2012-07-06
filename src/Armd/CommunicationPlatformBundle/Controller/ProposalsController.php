<?php

namespace Armd\CommunicationPlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\SecurityContext;

use Armd\CommunicationPlatformBundle\Entity\Proposals;
use Armd\CommunicationPlatformBundle\Form\ProposalsType;
use Armd\CommentBundle\Entity\Thread;

/**
 * Proposals controller.
 *
 */
class ProposalsController extends Controller
{
    /**
     * @var Symfony\Component\Security\Core\SecurityContext
     */
    protected $securityContext;

    /**
     * Lists all Proposals entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ArmdCommunicationPlatformBundle:Proposals')->findAll();

        return $this->render('ArmdCommunicationPlatformBundle:Proposals:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * Finds and displays a Proposals entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ArmdCommunicationPlatformBundle:Proposals')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proposals entity.');
        }

        $thread = $this->getThread($entity);
        $comments = $this->getComments($thread);

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ArmdCommunicationPlatformBundle:Proposals:show.html.twig', array(
            'comments'    => $comments,
            'thread'      => $thread,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Displays a form to create a new Proposals entity.
     *
     */
    public function newAction()
    {
        $entity = new Proposals();
        $form   = $this->createForm(new ProposalsType(), $entity);

        return $this->render('ArmdCommunicationPlatformBundle:Proposals:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Proposals entity.
     *
     */
    public function createAction()
    {
        $entity  = new Proposals();
        $request = $this->getRequest();
        $form    = $this->createForm(new ProposalsType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $entity->setThread($this->createThread());
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $this->setAcl($entity);

            return $this->redirect($this->generateUrl('cp_show', array('id' => $entity->getId())));
        }

        return $this->render('ArmdCommunicationPlatformBundle:Proposals:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Proposals entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ArmdCommunicationPlatformBundle:Proposals')->find($id);
        $this->securityCheck('EDIT', $entity);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proposals entity.');
        }

        $editForm = $this->createForm(new ProposalsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ArmdCommunicationPlatformBundle:Proposals:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Proposals entity.
     *
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ArmdCommunicationPlatformBundle:Proposals')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proposals entity.');
        }

        $editForm   = $this->createForm(new ProposalsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cp_edit', array('id' => $id)));
        }

        return $this->render('ArmdCommunicationPlatformBundle:Proposals:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Proposals entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ArmdCommunicationPlatformBundle:Proposals')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Proposals entity.');
            }

            $this->securityCheck('DELETE', $entity);
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cp'));
    }

    /**
     * @param $id
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * @param int|null $threadId
     * @return Armd\CommentBundle\Entity\Thread
     */
    public function createThread()
    {
        $thread = $this->container->get('fos_comment.manager.thread')->createThread();
        $thread->setPermalink($this->getRequest()->getUri());

        // Add the thread
        $this->container->get('fos_comment.manager.thread')->saveThread($thread);

        return $thread;
    }

    /**
     * @param Armd\CommentBundle\Entity\Thread $thread
     * @return Armd\CommentBundle\Entity\Comment
     */
    public function getComments(Thread $thread)
    {
        return $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);
    }

    /**
     * @param \Armd\CommunicationPlatformBundle\Entity\Proposals $entity
     * @return Armd\CommentBundle\Entity\Thread
     */
    public function getThread(Proposals $entity)
    {
        return $this->container->get('fos_comment.manager.thread')->findThreadById($entity->getThread()->getId());
    }

    /**
     * @param Proposals $entity
     */
    public function setAcl(Proposals $entity)
    {
        // creating the ACL
        $aclProvider = $this->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $acl = $aclProvider->createAcl($objectIdentity);

        $user = $this->getSecurityContext()->getToken()->getUser();
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // grant owner access
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $aclProvider->updateAcl($acl);
    }

    /**
     * @param string $action
     * @param \Armd\CommunicationPlatformBundle\Entity\Proposals $entity
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function securityCheck($action, Proposals $entity)
    {
        if ( false === $this->getSecurityContext()->isGranted(strtoupper($action), $entity) ) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @return Symfony\Component\Security\Core\SecurityContext
     */
    public function getSecurityContext()
    {
        if ( null === $this->securityContext ) {
            $this->securityContext = $this->get('security.context');
        }

        return $this->securityContext;
    }
}
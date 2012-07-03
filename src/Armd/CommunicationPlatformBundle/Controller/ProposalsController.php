<?php

namespace Armd\CommunicationPlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use FOS\CommentBundle\Entity\Thread;

use Armd\CommunicationPlatformBundle\Entity\Proposals;
use Armd\CommunicationPlatformBundle\Form\ProposalsType;
use Armd\CommunicationPlatformBundle\Entity\Comment;

/**
 * Proposals controller.
 *
 */
class ProposalsController extends Controller
{
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

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ArmdCommunicationPlatformBundle:Proposals:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
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
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById(10);
        $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        $em = $this->getDoctrine()->getManager();

        /**
         * @var $entity Proposals
         */
        $entity = $em->getRepository('ArmdCommunicationPlatformBundle:Proposals')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proposals entity.');
        }

        $editForm = $this->createForm(new ProposalsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ArmdCommunicationPlatformBundle:Proposals:edit.html.twig', array(
            'comments' => $comments,
            'thread' => $thread,
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

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cp'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * @param int|null $threadId
     * @return Thread
     */
    public function createThread()
    {
        $thread = $this->container->get('fos_comment.manager.thread')->createThread();
        $thread->setId(10);
        $thread->setPermalink($this->getRequest()->getUri());

        // Add the thread
        $this->container->get('fos_comment.manager.thread')->saveThread($thread);

        return $thread;
    }

    /**
     * @param Thread $thread
     * @return Comment
     */
    public function getComments(Thread $thread)
    {
        return $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);
    }
}

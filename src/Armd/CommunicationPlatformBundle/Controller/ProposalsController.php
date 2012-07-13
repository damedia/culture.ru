<?php

namespace Armd\CommunicationPlatformBundle\Controller;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
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
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Armd\CommunicationPlatformBundle\Acl\SecurityProposalsAcl
     */
    protected $proposalsAcl;

    /**
     * Lists all Proposals entities.
     *
     */
    public function indexAction($topic, $sort, $order, $page)
    {
        $entities =$this->getPagination(
            $this->getEntityManager()->getRepository('ArmdCommunicationPlatformBundle:Proposals')
                ->getQueryListProposals($topic, $sort, $order),
            $page, 10);

        $topis = $this->getEntityManager()->getRepository('ArmdCommunicationPlatformBundle:Topic')->findAll();

        return $this->render('ArmdCommunicationPlatformBundle:Proposals:index.html.twig', array(
            'statistic'    => $this->getStatisticInfomation($entities->getTotalItemCount()),
            'current_topic'=> $topic,
            'current_sort' => $sort,
            'topics'       => $topis,
            'entities'     => $entities
        ));
    }

    /**
     * Finds and displays a Proposals entity.
     *
     */
    public function showAction($id)
    {
        $entity = $this->getEntityManager()->getRepository('ArmdCommunicationPlatformBundle:Proposals')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Proposals entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $topics = $this->getEntityManager()->getRepository('ArmdCommunicationPlatformBundle:Topic')->findAll();

        return $this->render('ArmdCommunicationPlatformBundle:Proposals:show.html.twig', array(
            'statistic'    => $this->getStatisticInfomation(
                $this->getEntityManager()->getRepository('ArmdCommunicationPlatformBundle:Proposals')->getCountProposals($entity->getTopic()->getId())),
            'topics'      => $topics,
            'comments'    => $this->getComments($entity->getThread()),
            'thread'      => $entity->getThread(),
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
            $entity->setVoteObjectThread($this->createVoteObjectThread());
            $entity->setAuthor($this->getSecurityContext()->getToken()->getUser());

            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();

            $this->getProposalsAcl()->setDefaultAcl($entity);

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
        $entity = $this->getEntityManager()->getRepository('ArmdCommunicationPlatformBundle:Proposals')->find($id);
        $this->getProposalsAcl()->canEdit($entity);

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
     * Deletes a Proposals entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $form->bindRequest($this->getRequest());

        if ($form->isValid()) {
            $entity = $this->getEntityManager()->getRepository('ArmdCommunicationPlatformBundle:Proposals')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Proposals entity.');
            }

            $this->getProposalsAcl()->canDelete($entity);
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
        }

        return $this->redirect($this->generateUrl('cp'));
    }

    /**
     * @param \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $entities
     * @return array
     */
    public function getStatisticInfomation($count_entities)
    {
        $simpeUsers = 0;
        $expertUsers = 0;
        $userManager = $this->get('fos_user.user_manager');

        foreach ($userManager->getUsersRoles() as $user) {
            if ($user->hasRole('ROLE_EXPERT')) {
                $expertUsers++;
            }
            if ($user->hasRole('ROLE_USER') && !$user->hasRole('ROLE_EXPERT')) {
                $simpeUsers++;
            }
        }

        return array('online_users' => $userManager->getCountOnlineUsers(),
                     'simpleUser'   => $simpeUsers,
                     'expertUsers'  => $expertUsers,
                     'totalCount'   => $count_entities);
    }

    /**
     * @param $id
     * @return \Symfony\Component\Form\Form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }

    /**
     * @param int|null $threadId
     * @return Armd\CommentBundle\Entity\Thread
     */
    public function createThread()
    {
        $thread = $this->container->get('fos_comment.manager.thread')->createThread();
        $thread->setPermalink($this->getRequest()->getUri());

        $this->container->get('fos_comment.manager.thread')->saveThread($thread);

        return $thread;
    }

    /**
     * @return \Armd\CommentBundle\Entity\VoteObjectThread
     */
    public function createVoteObjectThread()
    {
        $thread = $this->container->get('armd_comment.manager.vote_thread_object')->createThread();
        $this->container->get('armd_comment.manager.vote_thread_object')->saveThread($thread);

        return $thread;
    }

    /**
     * @param Armd\CommentBundle\Entity\Thread $thread
     * @return \Armd\CommentBundle\Entity\Comment
     */
    public function getComments(Thread $thread)
    {
        return $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);
    }

    /**
     * @param \Doctrine\ORM\Query $query
     * @param int $page
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    public function getPagination(\Doctrine\ORM\Query $query, $page, $limit)
    {
        $paginator = $this->get('knp_paginator');

        return $paginator->paginate($query, $page, $limit);
    }

    /**
     * @return Symfony\Component\Security\Core\SecurityContext
     */
    public function getSecurityContext()
    {
        if (null === $this->securityContext) {
            $this->securityContext = $this->get('security.context');
        }

        return $this->securityContext;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getDoctrine()->getManager();
        }

        return $this->em;
    }

    /**
     * @return \Armd\CommunicationPlatformBundle\Acl\SecurityProposalsAcl
     */
    public function getProposalsAcl()
    {
        if (null === $this->proposalsAcl) {
            $this->proposalsAcl = $this->get('armd_cp.acl.proposals.security');
        }

        return $this->proposalsAcl;
    }
}
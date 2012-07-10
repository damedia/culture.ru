<?php

namespace Armd\CommentBundle\Controller;

use FOS\RestBundle\View\RouteRedirectView;
use FOS\CommentBundle\Controller\ThreadController as BaseController;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\RestBundle\View\View;
use FOS\Rest\Util\Codes;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\HttpFoundation\Response;

use Armd\CommentBundle\Entity\VoteObjectThread;
use Armd\CommentBundle\Entity\Comment;
use Armd\CommentBundle\Entity\VoteObject;
use Armd\CommentBundle\Entity\Vote;

/**
 * Restful controller for the Threads.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class ThreadController extends BaseController
{
    /**
     * @var \Armd\CommentBundle\Acl\AclCommentManager
     */
    protected $commentManger;

    /**
     * @var \Armd\CommentBundle\Acl\AclVoteManager
     */
    protected $voteManger;
    /**
     * Presents the form to use to create a new Vote for a Comment.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function newThreadCommentVotesAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->getCommentManager()->findCommentById($commentId);
        $value = $this->getRequest()->query->get('value', 1);

        if (!(($token = $this->get('security.context')->getToken()) instanceof AnonymousToken)) {
            foreach ($this->getVotes($comment) as $commentVote) {
                if (($commentVote->getVoter() == $token->getUser())) {
                    if ($commentVote->getValue() == (-1)*$value) {
                        $this->clearOppositeVote($comment, $commentVote, $value);
                        break;
                    }
                    return $this->setExeptionResponse('voted', 200);
                }
            }
        }

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $vote = $this->getVoteManager()->createVote($comment);
        $vote->setValue($value);

        $form = $this->container->get('fos_comment.form_factory.vote')->createForm();
        $form->setData($vote);

        $view = View::create()
            ->setData(array(
                'id' => $id,
                'commentId' => $commentId,
                'form' => $form->createView()
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'vote_new'));

        return $view;
    }

    /**
     * @param $id
     * @return \FOS\RestBundle\View\View|\Symfony\Component\HttpFoundation\Response
     */
    public function newVoteObjectThreadVotesAction($id)
    {
        $thread = $this->container->get('armd_comment.manager.vote_thread_object')->findThreadById($id);
        $value = $this->getRequest()->query->get('value', 1);

        if (!(($token = $this->get('security.context')->getToken()) instanceof AnonymousToken)) {
            foreach ($this->getObjectVotes($thread) as $objectVote) {
                if (($objectVote->getVoter() == $token->getUser())) {
                    if ($objectVote->getValue() == (-1)*$value) {
                        $this->clearOppositeObjectVote($thread, $objectVote, $value);
                        break;
                    }
                    return $this->setExeptionResponse('voted', 200);
                }
            }
        }

        $vote = $this->getVoteObjectManager()->createVote($thread);
        $vote->setValue($value);

        $form = $this->container->get('armd_comment.form_factory.vote_object')->createForm();
        $form->setData($vote);

        $view = View::create()
            ->setData(array(
                'id' => $id,
                'form' => $form->createView()
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'vote_object_new'));

        return $view;
    }

    /**
     * Creates a new Vote for the Comment from the submitted data.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function postThreadObjectVotesAction($id)
    {
        $thread = $this->container->get('armd_comment.manager.vote_thread_object')->findThreadById($id);

        if (null === $thread) {
            throw new NotFoundHttpException(sprintf("No VoteObjectThread with id '%s' found", $id));
        }

        $vote = $this->getVoteObjectManager()->createVote($thread);

        $form = $this->container->get('armd_comment.form_factory.vote_object')->createForm();
        $form->setData($vote);

        $form->bindRequest($this->container->get('request'));

        if ($form->isValid()) {

            $this->getVoteObjectManager()->saveVote($vote);

            return $this->onCreateVoteObjectSuccess($form, $id);
        }

        return $this->onCreateVoteObjectError($form, $id);
    }

    /**
     * Get the votes of a comment.
     *
     * @param string $id        Id of the thread
     *
     * @return View
     */
    public function getThreadObjectVotesAction($id)
    {
        $thread = $this->container->get('armd_comment.manager.vote_thread_object')->findThreadById($id);

        if (null === $thread) {
            throw new NotFoundHttpException(sprintf("No thread with id '%s' found", $id));
        }

        $view = View::create()
            ->setData(array(
                'objectScore' => $thread->getScore(),
            ))
            ->setTemplate(new TemplateReference('ArmdCommentBundle', 'Thread', 'object_votes'));

        return $view;
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    protected function onCreateVoteObjectSuccess(FormInterface $form, $id)
    {
        return RouteRedirectView::create('fos_comment_get_thread_object_votes', array('id' => $id));
    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param $id
     * @return \FOS\RestBundle\View\View
     */
    protected function onCreateVoteObjectError(FormInterface $form, $id)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                'id' => $id,
                'form' => $form,
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'vote_object_new'));

        return $view;
    }

    /**
     * @param $errorMessage
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setExeptionResponse($errorMessage = 'access denied', $errorCode = 403)
    {
        return new Response(
            json_encode(array(
                'status' => $errorCode,
                'error' => $errorMessage,
            )),
            $errorCode,
            array('Content-Type' => 'application/json'));
    }

    /**
     * @param \FOS\CommentBundle\Model\CommentInterface $comment
     * @return \Armd\CommentBundle\Entity\Vote[]
     */
    public function getVotes(CommentInterface $comment)
    {
        return $this->getVoteManager()->findVotesByComment($comment);
    }

    /**
     * @return \Armd\CommentBundle\Acl\AclVoteManager
     */
    public function getVoteManager()
    {
        if (null == $this->voteManger) {
            $this->voteManger = $this->container->get('fos_comment.manager.vote');
        }

        return $this->voteManger;
    }

    /**
     * @param \Armd\CommentBundle\Entity\VoteObjectThread $thread
     * @return \Armd\CommentBundle\Entity\VoteObjectManager[]
     */
    public function getObjectVotes(VoteObjectThread $thread)
    {
        return $this->getVoteObjectManager()->findObjectVotesByThread($thread);
    }

    /**
     * @return \Armd\CommentBundle\Entity\VoteObjectManager
     */
    public function getVoteObjectManager()
    {
        if (null == $this->voteManger) {
            $this->voteManger = $this->container->get('armd_comment.manager.vote_object');
        }

        return $this->voteManger;
    }

    /**
     * @return \Armd\CommentBundle\Acl\AclCommentManager
     */
    public function getCommentManager()
    {
        if (null == $this->commentManger) {
            $this->commentManger = $this->container->get('fos_comment.manager.comment');
        }

        return $this->commentManger;
    }

    /**
     * @param \Armd\CommentBundle\Entity\Comment $comment
     * @param \Armd\CommentBundle\Entity\Vote $commentVote
     * @param int $value
     */
    public function clearOppositeVote(Comment $comment, Vote $commentVote, $value)
    {
        $this->getVoteManager()->removeVote($commentVote);
        $comment->incrementScore($value);
        $this->getCommentManager()->updateComment($comment);
    }

    /**
     * @param \Armd\CommentBundle\Entity\VoteObjectThread $thread
     * @param \Armd\CommentBundle\Entity\VoteObject $objectVote
     * @param $value
     */
    public function clearOppositeObjectVote(VoteObjectThread $thread, VoteObject $objectVote, $value)
    {
        $this->getVoteObjectManager()->removeVote($objectVote);
        $thread->incrementScore($value);
        $thread->decrementCountVotes();
        $this->container->get('armd_comment.manager.vote_thread_object')->saveThread($thread);
    }
}

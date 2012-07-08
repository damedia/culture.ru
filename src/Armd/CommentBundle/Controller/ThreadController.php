<?php

namespace Armd\CommentBundle\Controller;

use FOS\CommentBundle\Controller\ThreadController as BaseController;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\RestBundle\View\View;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\HttpFoundation\Response;

use Armd\CommentBundle\Entity\Comment;
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
}

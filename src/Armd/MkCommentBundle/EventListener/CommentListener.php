<?php
namespace Armd\MkCommentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use FOS\CommentBundle\Model\CommentManagerInterface;
use Armd\MkCommentBundle\Entity\CommentManager;
use Symfony\Component\DependencyInjection\Container;
use Armd\MkCommentBundle\Entity\Comment;
use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\CommentBundle\EventListener\CommentBlamerListener;
use FOS\CommentBundle\Model\CommentInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentEvent;

class CommentListener implements EventSubscriberInterface
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

//    public function preUpdate(PreUpdateEventArgs $args)
//    {
//        $comment = $args->getEntity();
//        if ($comment instanceof Comment) {
//            $commentManager = $this->container->get('fos_comment.manager.comment');
//            $entityManager = $this->container->get('doctrine')->getManager();
//
//            $this->calcThreadComments($comment, $commentManager, $entityManager);
//        }
//    }
//
//    public function prePersist(LifecycleEventArgs $args)
//    {
//        $comment = $args->getEntity();
//        if ($comment instanceof Comment) {
//
//            $logger = $this->container->get('logger');
//            $securityContext = $this->container->get('security.context');
//            $commentManager = $this->container->get('fos_comment.manager.comment');
//            $entityManager = $this->container->get('doctrine')->getManager();
//
//            $this->blame($comment, $securityContext, $logger);
//            $this->autoModerate($comment);
//            $this->calcThreadComments($comment, $commentManager, $entityManager);
//        }
//    }
//

    public function onCommentPersist(CommentEvent $event)
    {
        $comment = $event->getComment();

        $logger = $this->container->get('logger');
        $securityContext = $this->container->get('security.context');
        $commentManager = $this->container->get('fos_comment.manager.comment');
        $entityManager = $this->container->get('doctrine')->getManager();

        if($commentManager->isNewComment($comment))
        {
            $this->blame($comment, $securityContext, $logger);
            $this->autoModerate($comment);
        }
        $this->calcThreadComments($comment, $commentManager, $entityManager);
    }


    protected function blame($comment, SecurityContextInterface $securityContext, LoggerInterface $logger)
    {
        // call native foscomment blame listener, need to do it here because need control of events order
        $commentBlamerListener = new CommentBlamerListener($securityContext, $logger);
        $event = new CommentEvent($comment);
        $commentBlamerListener->blame($event);

    }

    protected function autoModerate(Comment $comment)
    {
        if(!$comment->getSkipAutoModerate()) {
            if ($comment->getAuthor()->hasRole('ROLE_ADMIN')) {
                $comment->setState(CommentInterface::STATE_VISIBLE);
            } else {
                $comment->setState(CommentInterface::STATE_PENDING);
            }
        }
    }


    protected function calcThreadComments(
        Comment $comment,
        CommentManagerInterface $commentManager,
        EntityManager $entityManager
    ) {
        $thread = $comment->getThread();

        if (($commentManager->isNewComment($comment) || $comment->getPreviousState() !== $comment::STATE_VISIBLE)
            && $comment->getState() === $comment::STATE_VISIBLE
        ) {
            $thread->incrementNumComments(1);
        } elseif (!$commentManager->isNewComment($comment) && $comment->getPreviousState() === $comment::STATE_VISIBLE
            && $comment->getState() !== $comment::STATE_VISIBLE
        ) {
            $thread->incrementNumComments(-1);
        }

        if ($commentManager->isNewComment($comment)) {
            $thread->setLastCommentAt($comment->getCreatedAt());
        }

        $entityManager->persist($thread);
    }


    public static function getSubscribedEvents()
    {
        return array(Events::COMMENT_PRE_PERSIST => 'onCommentPersist');
    }

}
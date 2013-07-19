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
use Armd\MkCommentBundle\Entity\Notice;

class CommentListener implements EventSubscriberInterface
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        if($entity instanceof Comment) {
            $commentManager = $this->container->get('fos_comment.manager.comment.default'); // fos_comment.manager.comment.default
            $thread = $entity->getThread();
            $commentManager->recalculateThreadCommentCount($thread);
            $commentManager->recalculateThreadLastComment($thread);
            $eventArgs->getEntityManager()->flush();
        }
    }

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
            $this->notifyModerators($comment);
            $this->makeNoticies($comment);
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
            $comment->setState(CommentInterface::STATE_VISIBLE);
//            if ($comment->getAuthor()->hasRole('ROLE_ADMIN')) {
//                $comment->setState(CommentInterface::STATE_VISIBLE);
//            } else {
//                $comment->setState(CommentInterface::STATE_PENDING);
//            }
        }
    }
    
    protected function makeNoticies($comment)
    {
        $em = $this->container->get('doctrine')->getManager();
        
        // check `notice_replies_to_comment`
        if(sizeof($ancestors = $comment->getAncestors()) && ($parentId = array_pop($ancestors))){
            $parent = $em->getRepository('\Armd\MkCommentBundle\Entity\Comment')->find($parentId);
            if($parent->getAuthor() !== $comment->getAuthor() && $parent->getAuthor()->getNoticeOnComment() === Notice::T_REPLY){
                $em->persist($this->createNotice(Notice::T_REPLY, $comment, $parent->getAuthor()));
            }
        }
        
        // check `notice_replies_to_threads`
        $qb2 = $em->getRepository('\Armd\MkCommentBundle\Entity\Comment')->createQueryBuilder('c');
        $qb2->select('IDENTITY(c.author)')->distinct()
            ->where('c.thread = :tid')
            ->andWhere('c.state = :state')
            ->andWhere('c.author <> :uid');
        $qb = $em->getRepository('\Armd\UserBundle\Entity\User')->createQueryBuilder('u');
        $qb->select('u')
            ->where($qb->expr()->in('u.id', $qb2->getDQL()))
            ->andWhere('u.enabled = true')
            ->andWhere('u.noticeOnComment = :ctype')
            ->setParameter('tid', $comment->getThread()->getId())
            ->setParameter('state', Comment::STATE_VISIBLE)
            ->setParameter('ctype', Notice::T_THREAD)
            ->setParameter('uid', $comment->getAuthor()->getId());
        $users = $qb->getQuery()->getResult();
        foreach($users as $user){
            if($user->getId() !== $comment->getAuthor()->getId()){
                $em->persist($this->createNotice(Notice::T_THREAD, $comment, $user));
            }
        }
        
        // check `notice_all_new_comments`
        /* Should be much faster if rewrite in native 'INSERT INTO comment_notice SELECT ... FROM fos_user_user WHERE ... ' */
        $users = $em->getRepository('\Armd\UserBundle\Entity\User')->findBy(array('noticeOnComment' => Notice::T_ALL));
        foreach($users as $user){
            if($user->getId() !== $comment->getAuthor()->getId()){
                $em->persist($this->createNotice(Notice::T_ALL, $comment, $user));
            }
        }
        
        $em->flush();        
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

    protected function notifyModerators($comment)
    {
        // Получить список модераторов
        $userManager = $this->container->get('fos_user.user_manager.default');
        $moderators = $userManager->getModerators();
        
        if ($moderators) {
            // Посылаем email модераторам
            foreach ($moderators as $moderator) {
                $emailFrom = $this->container->getParameter('mail_from');
                $emailTo   = $moderator->getEmail();
                $subject   = 'Новый комментарий';
                $template  = 'ArmdMkCommentBundle:Email:notifyModeratorMessage.html.twig';
                $body = $this->container->get('templating')->render($template, array(
                    'comment' => $comment,
                ));
                $message = \Swift_Message::newInstance()
                    ->setSubject($subject)
                    ->setFrom($emailFrom)
                    ->setTo($emailTo)
                    ->setBody($body)
                    ->setContentType("text/html")
                ;
                $this->container->get('mailer')->send($message);
            }
        }
        return true;
    }

    protected function createNotice($type, $comment, $owner)
    {
        $notice = new Notice();
        $notice->setUser($owner);
        $notice->setType($type);
        $notice->setCreatedAt(new \DateTime());
        $notice->setComment($comment);
        return $notice;
    }    
}
<?php
namespace Armd\MkCommentBundle\Entity;

use FOS\CommentBundle\Entity\CommentManager as BaseCommentManager;
use FOS\CommentBundle\Event\CommentPersistEvent;
use FOS\CommentBundle\Events;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\ThreadInterface;

class CommentManager extends BaseCommentManager
{

    /**
     * Returns a flat array of comments of a specific thread.
     *
     * @param  ThreadInterface $thread
     * @param  integer         $depth
     * @return array           of ThreadInterface
     */
    public function findCommentsByThread(ThreadInterface $thread, $depth = null, $sorterAlias = null)
    {
        $qb = $this->repository
            ->createQueryBuilder('c')
            ->join('c.thread', 't')
            ->where('t.id = :thread')
            ->andWhere('c.state = :visible_state')
            ->orderBy('c.ancestors', 'ASC')
            ->setParameter('thread', $thread->getId())
            ->setParameter('visible_state', CommentInterface::STATE_VISIBLE)
        ;


        if (null !== $depth && $depth >= 0) {
            // Queries for an additional level so templates can determine
            // if the final 'depth' layer has children.

            $qb->andWhere('c.depth < :depth')
                ->setParameter('depth', $depth + 1);
        }

        $comments = $qb
            ->getQuery()
            ->execute();

        $this->removeOrphanComments($comments);

        if (null !== $sorterAlias) {
            $sorter = $this->sortingFactory->getSorter($sorterAlias);
            $comments = $sorter->sortFlat($comments);
        }

        return $comments;
    }

    /**
     * Returns the requested comment tree branch
     *
     * @param  integer $commentId
     * @param  string  $sorter
     * @return array   See findCommentTreeByThread
     */
    public function findCommentTreeByCommentId($commentId, $sorter = null)
    {
        $qb = $this->repository->createQueryBuilder('c');
        $qb->join('c.thread', 't')
            ->where('LOCATE(:path, CONCAT(\'/\', CONCAT(c.ancestors, \'/\'))) > 0')
            ->andWhere('c.state = :visible_state')
            ->orderBy('c.ancestors', 'ASC')
            ->setParameter('path', "/{$commentId}/")
            ->setParameter('visible_state', CommentInterface::STATE_VISIBLE)
        ;

        $comments = $qb->getQuery()->execute();

        if (!$comments) {
            return array();
        }

        $sorter = $this->sortingFactory->getSorter($sorter);

        $trimParents = current($comments)->getAncestors();

        return $this->organiseComments($comments, $sorter, $trimParents);
    }

    protected function removeOrphanComments(&$comments)
    {
        $ids = array();
        foreach($comments as $comment) {
            $ids[$comment->getId()] = 1;
        }

        do {
            $needAnotherPass = false;
            foreach($comments as $key => $comment) {
                $ancestors = $comment->getAncestors();
                if (!empty($ancestors)) {
                    $leaveThis = false;
                    foreach($ancestors as $ancestor) {
                        if(!empty($ids[$ancestor])) {
                            $leaveThis = true;
                            break;
                        }
                    }
                    if (!$leaveThis) {
                        unset($comments[$key]);
                        $needAnotherPass = true;
                    }
                }
            }
        } while($needAnotherPass);
    }

//    public function autoModerate(CommentInterface $comment)
//    {
//        $roles = $comment->getAuthor()->getRoles();
//
//        if (in_array('ROLE_ADMIN', $roles, true)) {
//            $comment->setState(CommentInterface::STATE_VISIBLE);
//        } else {
//            $comment->setState(CommentInterface::STATE_PENDING);
//        }
//    }

}
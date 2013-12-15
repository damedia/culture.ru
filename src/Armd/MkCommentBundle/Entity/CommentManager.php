<?php
namespace Armd\MkCommentBundle\Entity;

use FOS\UserBundle\Model\UserInterface;
use FOS\CommentBundle\Entity\CommentManager as BaseCommentManager;
use FOS\CommentBundle\Event\CommentPersistEvent;
use FOS\CommentBundle\Events;
use FOS\CommentBundle\Model\ThreadInterface;
use Armd\MkCommentBundle\Model\CommentInterface;

class CommentManager extends BaseCommentManager {

    /**
     * Returns a flat array of comments of a specific thread.
     *
     * This method is overridden to change literally two lines (visible_state in query).
     *
     * @param  ThreadInterface $thread
     * @param  integer         $depth
     * @param  string          $sorterAlias
     * @return array           of ThreadInterface
     */
    public function findCommentsByThread(ThreadInterface $thread, $depth = null, $sorterAlias = null) {
        $qb = $this->repository
            ->createQueryBuilder('c')
            ->join('c.thread', 't')
            ->where('t.id = :thread')
            ->andWhere('c.state = :visible_state')
            ->orderBy('c.ancestors', 'ASC')
            ->setParameter('thread', $thread->getId())
            ->setParameter('visible_state', CommentInterface::STATE_VISIBLE);

        if (null !== $depth && $depth >= 0) {
            // Queries for an additional level so templates can determine
            // if the final 'depth' layer has children.
            $qb->andWhere('c.depth < :depth')->setParameter('depth', $depth + 1);
        }

        $comments = $qb->getQuery()->execute();
        $this->removeOrphanComments($comments);

        if ($sorterAlias !== null) {
            $sorter = $this->sortingFactory->getSorter($sorterAlias);
            $comments = $sorter->sortFlat($comments);
        }

        return $comments;
    }

    /**
     * Returns the requested comment tree branch
     * This method is overridden to change literally only one line.
     *
     * @param  integer $commentId
     * @param  string  $sorter
     * @return array   See findCommentTreeByThread
     */
    public function findCommentTreeByCommentId($commentId, $sorter = null) {
        $qb = $this->repository
            ->createQueryBuilder('c')
            ->join('c.thread', 't')
            ->where('LOCATE(:path, CONCAT(\'/\', CONCAT(c.ancestors, \'/\'))) > 0')
            ->andWhere('c.state = :visible_state')
            ->orderBy('c.ancestors', 'ASC')
            ->setParameter('path', "/{$commentId}/")
            ->setParameter('visible_state', CommentInterface::STATE_VISIBLE);

        $comments = $qb->getQuery()->execute();

        if (!$comments) {
            return array();
        }

        $sorter = $this->sortingFactory->getSorter($sorter);
        $trimParents = current($comments)->getAncestors();

        return $this->organiseComments($comments, $sorter, $trimParents);
    }
    
    public function findCommentsByUser(UserInterface $user)
    {
        $qb = $this->repository
            ->createQueryBuilder('c')
            ->join('c.thread', 't')
            ->where('c.author = :userid')
            ->andWhere('c.state = :visible_state')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('userid', $user->getId())
            ->setParameter('visible_state', CommentInterface::STATE_VISIBLE)
        ;
        $comments = $qb->getQuery()->execute();
        foreach($comments as $comment){
            $comment->setThreadCrumbs($this->getThreadCrumbsByComment($comment));
        }
        return $comments;
    }
    
    public function findNoticesForUser(UserInterface $user)
    {
        $qb = $this->em->getRepository('\Armd\MkCommentBundle\Entity\Notice')
            ->createQueryBuilder('n')
            ->join('n.comment', 'c')
            ->join('c.thread', 't')
            ->where('n.user = :userid')
            ->andWhere('c.state = :visible_state')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('userid', $user->getId())
            ->setParameter('visible_state', CommentInterface::STATE_VISIBLE)
        ;
        $notices = $qb->getQuery()->execute();
        foreach($notices as $notice){
            $notice->getComment()->setThreadCrumbs($this->getThreadCrumbsByComment($notice->getComment()));
        }
        return $notices;
    }
    
    public function deleteUserNotices(UserInterface $user)
    {
        return $this->em->createQueryBuilder()->delete('\Armd\MkCommentBundle\Entity\Notice', 'n')
            ->where('n.user = :uid')
            ->setParameter('uid', $user->getId())
            ->getQuery()
            ->execute();
    }
    
    public function getThreadCrumbsByComment(Comment $comment)
    {
        $tmp = explode('_', $comment->getThread()->getId(), 3);
        $section = array(
            'locale' => $tmp[0],
            'section' => $tmp[1],
            'id' => $tmp[2],
        );
        switch($section['section']){
            case CommentInterface::SECTION_LECTURE:
                $entityName = '\Armd\LectureBundle\Entity\Lecture';
                $section['threadTitle'] = 'menu.cinema';
                $section['threadPath']  = 'armd_lecture_cinema_index';
                $section['entityPath']  = 'armd_lecture_view';
                break;
            case CommentInterface::SECTION_LESSON:
                $entityName = '\Armd\MuseumBundle\Entity\Lesson';
                $section['threadTitle'] = 'menu.museum_lesson';
                $section['threadPath'] = 'armd_lesson_list';
                $section['entityPath'] = 'armd_lesson_item';
                break;
            case CommentInterface::SECTION_ROUTE:
                $entityName = '\Armd\TouristRouteBundle\Entity\Route';
                $section['threadTitle'] = 'menu.tourist_routes';
                $section['threadPath'] = 'armd_tourist_route_list';
                $section['entityPath'] = 'armd_tourist_route_item';
                break;
            case CommentInterface::SECTION_THEATER:
                $entityName = '\Armd\TheaterBundle\Entity\Theater';
                $section['threadTitle'] = 'menu.theaters';
                $section['threadPath'] = 'armd_theater_list';
                $section['entityPath'] = 'armd_theater_item';
                break;
            case CommentInterface::SECTION_PERFOMANCE:
                $entityName = '\Armd\PerfomanceBundle\Entity\Perfomance';
                $section['threadTitle'] = 'menu.perfomance';
                $section['threadPath'] = 'armd_perfomance_list';
                $section['entityPath'] = 'armd_perfomance_item';
                break;
            default: return false;
        }
        if(!($entity = $this->em->getRepository($entityName)->find($section['id'])))
            return false;
        
        if($section['section'] == CommentInterface::SECTION_LECTURE && $entity->getLectureSuperType()->getCode() === 'LECTURE_SUPER_TYPE_LECTURE'){
            $section['threadTitle'] =  'menu.lectures';
            $section['threadPath'] =  'armd_lecture_lecture_index';
        }
        $section['entityTitle'] = $entity->getTitle();
        return $section;
    }


    public function recalculateThreadCommentCount(Thread $thread)
    {
        $comments = $this->findCommentsByThread($thread);
        $thread->setNumComments(count($comments));
    }

    public function recalculateThreadLastComment(Thread $thread)
    {
        $lastCommentDate = $this->em->createQueryBuilder()
            ->select('MAX(c.createdAt)')
            ->from('ArmdMkCommentBundle:Comment', 'c')
            ->where('c.thread = :thread')->setParameter('thread', $thread)
            ->getQuery()
            ->getSingleScalarResult();

        if($lastCommentDate) {
            $lastCommentDate = new \DateTime($lastCommentDate);
        }
        $thread->setLastCommentAt($lastCommentDate);
    }


    /**
     * Orphan comments are comments that are visible but their ancestor is not (for example not moderated)
     * @param $comments
     */
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
}
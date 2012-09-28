<?php
namespace Armd\MkCommentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FOS\CommentBundle\Model\CommentInterface;

class CommentRepository extends EntityRepository
{
    public function createQueryBuilder($alias)
    {
        $qb = parent::createQueryBuilder($alias);
        $qb->andWhere($alias.'.state = :visible_state')
            ->setParameter('visible_state', CommentInterface::STATE_VISIBLE);

        return $qb;
    }
}

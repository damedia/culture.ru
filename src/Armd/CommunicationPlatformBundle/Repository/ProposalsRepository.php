<?php
namespace Armd\CommunicationPlatformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class ProposalsRepository extends EntityRepository
{
    /**
     * @param $topic
     * @param $sort
     * @return \Doctrine\ORM\Query
     */
    public function getQueryListProposals($topic, $sort)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder('p, ct')
            ->select('p, ct, vt')
            ->from('ArmdCommunicationPlatformBundle:Proposals', 'p')
            ->join('p.thread', 'ct')
            ->join('p.voteObjectThread', 'vt')
            ->where('p.enabled = :enabled')
            ->setParameter('enabled', 1);

        $queryBuilder = $this->setTopicFilter($queryBuilder, $topic);
        $queryBuilder = $this->setOrder($queryBuilder, $sort);

        return $queryBuilder->getQuery();
    }

    /**
     * @param $topic
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function setTopicFilter(QueryBuilder $queryBuilder, $topic)
    {
        if ($topic) {
            $queryBuilder->andWhere('p.topic = :topic')
                         ->setParameter('topic', $topic);
        }

        return $queryBuilder;
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param $sort
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function setOrder(QueryBuilder $queryBuilder, $sort)
    {
        switch ($sort) {
            case 'rating':
                $queryBuilder->orderBy('vt.score', 'DESC');
                break;
            case 'created':
                $queryBuilder->orderBy('p.createdAt', 'DESC');
                break;
            case 'comments':
                $queryBuilder->orderBy('ct.numComments', 'DESC');
                break;
            default:
                $queryBuilder->orderBy('vt.score', 'DESC');
        }

        return $queryBuilder;
    }
}
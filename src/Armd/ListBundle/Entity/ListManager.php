<?php
namespace Armd\ListBundle\Entity;

use Doctrine\ORM\EntityManager;
use Armd\TagBundle\Entity\Tag;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineExtensions\Taggable\Taggable;
use Doctrine\ORM\QueryBuilder;
use Armd\TagBundle\Entity\TagManager;

abstract class ListManager
{
    protected $em;
    protected $tagManager;

    /** example: 10 */
    const CRITERIA_LIMIT = 'CRITERIA_LIMIT';

    /** example: 100  */
    const CRITERIA_OFFSET = 'CRITERIA_OFFSET';

    /** example: array('title' => 'ASC', 'createdAt' => 'DESC') */
    const CRITERIA_ORDER_BY = 'CRITERIA_ORDER_BY';

    /** example: true */
    const CRITERIA_RANDOM = 'CRITERIA_RANDOM';

    /** example: array('museum', 'world war') or array(new Tag(), new Tag())*/
    const CRITERIA_TAGS = 'CRITERIA_TAGS';

    const CRITERIA_TAG_ID = 'CRITERIA_TAG_ID';
    
    /** example: array(1, 2, 3) */
    const CRITERIA_NOT_IDS = 'CRITERIA_NOT_IDS';


    public function __construct(EntityManager $em, TagManager $tagManager)
    {
        $this->em = $em;
        $this->tagManager = $tagManager;
    }

    public function findObjects(array $criteria)
    {
        if (isset($criteria[self::CRITERIA_TAGS])) {
            if (empty($criteria[self::CRITERIA_LIMIT])) {
                throw new \LogicException('Criteria ObjectManager::CRITERIA_LIMIT must specified when searching with ObjectManager::CRITERIA_TAGS');
            }

            // find tagged objects
            $qb = $this->getQueryBuilder();
            $criteriaMod = $criteria;
            unset($criteriaMod[self::CRITERIA_LIMIT]);
            $this->setCriteria($qb, $criteriaMod);
            $objects = $this->getTaggedObjectsFromQueryBuilder($qb, $criteria[self::CRITERIA_TAGS], $criteria[self::CRITERIA_LIMIT]);

            // pad them
            if (count($objects) < $criteria[self::CRITERIA_LIMIT]) {
                $criteriaMod = $criteria;
                $criteriaMod[self::CRITERIA_LIMIT] = $criteriaMod[self::CRITERIA_LIMIT] - count($objects);
                unset($criteriaMod[self::CRITERIA_TAGS]);
                $paddingObjects = $this->findObjects($criteriaMod);
                $objects = array_merge($objects, $paddingObjects);
            }

        } elseif (!empty($criteria[self::CRITERIA_RANDOM])) {
            $qb = $this->getQueryBuilder();
            $this->setCriteria($qb, $criteria);

            $objects = $this->getRandomObjectsFromQueryBuilder($qb, $criteria[self::CRITERIA_LIMIT]);
        } else {
            $qb = $this->getQueryBuilder();
            $this->setCriteria($qb, $criteria);
            $paginator = new Paginator($qb, $fetchJoinCollection = true);
            $objects = array();
            foreach($paginator as $item) {
                $objects[] = $item;
            }
//            $objects = $paginator; //$qb->getQuery()->getResult();
        }

        return $objects;
    }

    /**
     * @return QueryBuilder
     */
    abstract public function getQueryBuilder();


    public function setCriteria(QueryBuilder $qb, $criteria)
    {
        $aliases = $qb->getRootAliases();
        $o = $aliases[0];

        if (!empty($criteria[self::CRITERIA_OFFSET])) {
            $qb->setFirstResult($criteria[self::CRITERIA_OFFSET]);
        }

        if (!empty($criteria[self::CRITERIA_LIMIT])) {
            $qb->setMaxResults($criteria[self::CRITERIA_LIMIT]);
        }

        if (!empty($criteria[self::CRITERIA_ORDER_BY])) {
            $qb->resetDQLPart('orderBy');
            foreach ($criteria[self::CRITERIA_ORDER_BY] as $k => $v) {
                $qb->addOrderBy("$o.$k", $v);
            }
        }
        
        if (!empty($criteria[self::CRITERIA_NOT_IDS])) {
            $notIds = $criteria[self::CRITERIA_NOT_IDS];
            
            if (is_array($notIds) && count($notIds) && $notIds != array(0)) {
                $qb->andWhere("$o.id NOT IN (:notIds)")->setParameter('notIds', $notIds);
            }
        }

        if (!empty($criteria[self::CRITERIA_TAG_ID])) {
            $qb->andWhere($qb->expr()->in(
                    "$o.id",
                    $this->em->getRepository($this->tagManager->getTaggingClass())
                        ->createQueryBuilder('tagging')
                        ->select('TOINT(tagging.resourceId)')
                        ->where('tagging.tag = :tag_id')
                        ->getDQL()
                ))
                ->setParameter('tag_id', $criteria[self::CRITERIA_TAG_ID]);

//            $qb->innerJoin($this->tagManager->getTaggingClass(), 'tagging', 'WITH', "TOINT(tagging.resourceId) = $o.id")
//                ->andWhere('tagging.tag  = :tag_id')->setParameter('tag_id', $criteria[self::CRITERIA_TAG_ID]);
        }
    }

    public function getTaggedObjectsFromQueryBuilder(QueryBuilder $qb, $tags, $limit)
    {
        if (empty($tags) || count($tags) < 1) {
            return array();
        }

        $tagWords = array();
        foreach ($tags as $tag) {
            if ($tag instanceof Tag) {
                $tagWords[] = $tag->getName();
            } else {
                $tagWords[] = $tag;
            }
        }

        $qbTag = clone($qb);

        $aliases = $qbTag->getRootAliases();
        $o = $aliases[0];

        $qbTag->innerJoin($this->tagManager->getTaggingClass(), 'tagging', 'WITH', "TOINT(tagging.resourceId) = $o.id")
            ->innerJoin('tagging.tag', 'tag')
            ->andWhere('tag.name IN (:tags)')->setParameter('tags', $tagWords)
            ->andWhere('tagging.resourceType = :resourceType')->setParameter('resourceType', $this->getTaggableType())
            ->addSelect("COUNT(tag) tagCount")
            ->addSelect("MAX(TOINT(tag.isTechnical)) tagIsTechnical")
            ->groupBy($o)
            ->orderBy('tagIsTechnical', 'DESC')
            ->addOrderBy('tagCount', 'DESC')
            ->setMaxResults($limit)
            ;

        $rows = $qbTag->getQuery()->getResult();
        $objects = array();
        foreach($rows as $row) {
            $objects[] = $row[0];
        }

        return $objects;
    }

    public function getRandomObjectsFromQueryBuilder(QueryBuilder $qb, $limit) {
        $qbCount = clone($qb);
        $aliases = $qbCount->getRootAliases();
        $o = $aliases[0];
        $objectCount = $qbCount->select("COUNT($o)")
            ->resetDQLPart('orderBy')
            ->getQuery()->getSingleScalarResult();

        if ($objectCount <= $limit) {
            $objects = $qb->getQuery()->getResult();
        }
        else {
            $offsets = array();
            for ($i = 0; $i < $limit; $i++) {
                $j = 0;
                do {
                    $offset = rand(0, $objectCount - 1);
                } while ($j++ < 10 && in_array($offset, $offsets));
                $offsets[] = $offset;
            }

            $objects = array();
            foreach ($offsets as $offset) {
                $objects[] = $qb->setMaxResults(1)
                    ->setFirstResult($offset)
                    ->getQuery()
                    ->getSingleResult();
            }
        }
        return $objects;
    }

    abstract public function getClassName();
    abstract public function getTaggableType();

}
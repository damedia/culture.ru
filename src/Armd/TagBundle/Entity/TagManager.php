<?php
namespace Armd\TagBundle\Entity;

use FPN\TagBundle\Entity\TagManager as BaseTagManager;

class TagManager extends BaseTagManager
{
    public function getResourceIdsByTags($resourceType, array $tags, $limit = 100)
    {
<<<<<<< HEAD
        $resourceIds = array();
        if (!empty($tags)) {
            $rows = $this->em->createQueryBuilder()
                ->select('tn.resourceId, COUNT(tn.resourceId) rcount')
                ->from($this->taggingClass, 'tn')
                ->innerJoin($this->tagClass, 't')
                ->where('t.name IN (:tags)')->setParameter('tags', $tags)
                ->andWhere('tn.resourceType = :resourceType')->setParameter('resourceType', $resourceType)
                ->groupBy('tn.resourceId')
                ->orderBy('rcount', 'DESC')
                ->setMaxResults($limit)
                ->getQuery()
                ->getScalarResult();

            foreach ($rows as $row) {
                $resourceIds[] = $row['resourceId'];
            }

        }
        return $resourceIds;
    }

    public function getTaggingClass()
    {
        return $this->taggingClass;
    }

    public function getTagClass()
    {
        return $this->tagClass;
    }

=======
        $rows = $this->em->createQueryBuilder()
            ->select('tn.resourceId, COUNT(tn.resourceId) rcount')
            ->from($this->taggingClass, 'tn')
            ->innerJoin($this->tagClass, 't')
            ->where('t.name IN (:tags)')->setParameter('tags', $tags)
            ->andWhere('tn.resourceType = :resourceType')->setParameter('resourceType', $resourceType)
            ->groupBy('tn.resourceId')
            ->orderBy('rcount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getScalarResult();

        $resourceIds = array();
        foreach ($rows as $row) {
            $resourceIds[] = $row['resourceId'];
        }
        return $resourceIds;
    }
>>>>>>> [+] atlast objects: search by tags
}
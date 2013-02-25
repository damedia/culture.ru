<?php
namespace Armd\TagBundle\Entity;

use FPN\TagBundle\Entity\TagManager as BaseTagManager;
use DoctrineExtensions\Taggable\Taggable;

class TagManager extends BaseTagManager
{
    public function getResourceIdsByTags($resourceType, array $tags, $limit = 100)
    {
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

//
//    public function loadOrCreateTag($name, $doFlush = true)
//    {
//        $tags = $this->loadOrCreateTags(array($name), $doFlush);
//        return $tags[0];
//    }
//
//    public function loadOrCreateTags(array $names, $doFlush = true)
//    {
//        if (empty($names)) {
//            return array();
//        }
//
//        $names = array_unique($names);
//
//        $builder = $this->em->createQueryBuilder();
//
//        $tags = $builder
//            ->select('t')
//            ->from($this->tagClass, 't')
//
//            ->where($builder->expr()->in('t.name', $names))
//
//            ->getQuery()
//            ->getResult()
//        ;
//
//        $loadedNames = array();
//        foreach ($tags as $tag) {
//            $loadedNames[] = $tag->getName();
//        }
//
//        $missingNames = array_udiff($names, $loadedNames, 'strcasecmp');
//        if (sizeof($missingNames)) {
//            foreach ($missingNames as $name) {
//                $tag = $this->createTag($name);
//                $this->em->persist($tag);
//
//                $tags[] = $tag;
//            }
//
//            if ($doFlush) {
//                $this->em->flush();
//            }
//        }
//
//        return $tags;
//    }
//
//    public function saveTagging(Taggable $resource, $doFlush = true)
//    {
//        $oldTags = $this->getTagging($resource);
//        $newTags = $resource->getTags();
//        $tagsToAdd = $newTags;
//
//        if ($oldTags !== null and is_array($oldTags) and !empty($oldTags)) {
//            $tagsToRemove = array();
//
//            foreach ($oldTags as $oldTag) {
//                if ($newTags->exists(function ($index, $newTag) use ($oldTag) {
//                    return $newTag->getName() == $oldTag->getName();
//                })) {
//                    $tagsToAdd->removeElement($oldTag);
//                } else {
//                    $tagsToRemove[] = $oldTag->getId();
//                }
//            }
//
//            if (sizeof($tagsToRemove)) {
//                $builder = $this->em->createQueryBuilder();
//                $builder
//                    ->delete($this->taggingClass, 't')
//                    ->where('t.tag_id')
//                    ->where($builder->expr()->in('t.tag', $tagsToRemove))
//                    ->andWhere('t.resourceType = :resourceType')
//                    ->setParameter('resourceType', $resource->getTaggableType())
//                    ->andWhere('t.resourceId = :resourceId')
//                    ->setParameter('resourceId', $resource->getTaggableId())
//                    ->getQuery()
//                    ->getResult()
//                ;
//            }
//        }
//
//        foreach ($tagsToAdd as $tag) {
//            $this->em->persist($tag);
//            $this->em->persist($this->createTagging($tag, $resource));
//        }
//
//        if (count($tagsToAdd)) {
//            if ($doFlush) {
//                $this->em->flush();
//            }
//        }
//    }

}
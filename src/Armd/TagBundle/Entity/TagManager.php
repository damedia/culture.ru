<?php
namespace Armd\TagBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * Loads or creates multiples tags from a list of tag names.
     * Overriden for case insensitive tag search.
     *
     * @param array  $names   Array of tag names
     * @return Tag[]
     */
    public function loadOrCreateTags(array $names)
    {
        if (empty($names)) {
            return array();
        }

        $names = array_unique($names);
        $lowerNames = array_map(function($v) { return mb_strtolower($v, mb_detect_encoding($v)); }, $names);

        $builder = $this->em->createQueryBuilder();

        $tags = $builder
            ->select('t')
            ->from($this->tagClass, 't')

            ->where($builder->expr()->in('LOWER(t.name)', $lowerNames))

            ->getQuery()
            ->getResult()
        ;

        $loadedNames = array();
        foreach ($tags as $tag) {
            $loadedNames[] = mb_strtolower($tag->getName(), mb_detect_encoding($tag->getName()));
        }

        foreach ($names as $name) {
            if (!in_array(mb_strtolower($name, mb_detect_encoding($name)), $loadedNames)) {
                $tag = $this->createTag($name);
                $this->em->persist($tag);

                $tags[] = $tag;
            }

            $this->em->flush();
        }

        return $tags;
    }

    /**
     * Saves tags for the given taggable resource
     *
     * @param Taggable  $resource   Taggable resource
     */
    public function saveTagging(Taggable $resource)
    {
        $tags = $resource->getTags();
        $uniqueTags = new ArrayCollection();
        foreach ($tags as $tag) {
            if ($uniqueTags->indexOf($tag) === false) {
                $uniqueTags->add($tag);
            }
        }
        $resource->setTags($uniqueTags);
        parent::saveTagging($resource);
    }

    public function getTaggingClass()
    {
        return $this->taggingClass;
    }

    public function getTagClass()
    {
        return $this->tagClass;
    }



}
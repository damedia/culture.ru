<?php
namespace Armd\TagBundle\Form\Loader;

use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Doctrine\ORM\EntityManager;

class TagLoader implements EntityLoaderInterface
{

    private $taggableType;
    private $manager;

    public function __construct($taggableType, EntityManager $manager)
    {
        $this->taggableType = $taggableType;
        $this->manager = $manager;
    }

    /**
     * Returns an array of entities that are valid choices in the corresponding choice list.
     *
     * @return array The entities.
     */
    public function getEntities()
    {
        $tags = $this->getQueryBuilder()->getQuery()->getResult();
        return $tags;
    }

    /**
     * Returns an array of entities matching the given identifiers.
     *
     * @param string $identifier The identifier field of the object. This method
     *                           is not applicable for fields with multiple
     *                           identifiers.
     * @param array $values The values of the identifiers.
     *
     * @return array The entities.
     */
    public function getEntitiesByIds($identifier, array $values)
    {
        $tags = $this->getQueryBuilder()->andWhere('t.tags in :tags')
            ->setParameter('tags', $values)
            ->getQuery()->getResult();
        return $tags;
    }

    protected function getQueryBuilder()
    {
        $qb = $this->manager->createQueryBuilder()
            ->select('t')
            ->from('ArmdTagBundle:Tag', 't')
            ->innerJoin('ArmdTagBundle:Tagging', 'tg')
            ->where('tg.resourceType = :resource_type')
            ->setParameter('resource_type', $this->taggableType)
        ;

        return $qb;
    }
}
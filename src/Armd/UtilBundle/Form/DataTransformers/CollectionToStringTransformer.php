<?php

namespace Armd\UtilBundle\Form\DataTransformers;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;

class CollectionToStringTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var class
     */
    private $class;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager, $class)
    {
        $this->objectManager = $objectManager;
        $this->class = $class;
    }

    /**
     * @param  ArrayCollection $collection
     * @return string
     */
    public function transform($collection)
    {
        if (null === $collection) {
            return '';
        }

        $result = array();

        foreach ($collection as $entity) {
            $result[] = $entity->getId();
        }

        return implode(',', $result);
    }

    /**
     * @param  string $string
     * @return ArrayCollection|null
     */
    public function reverseTransform($string)
    {
        if (!$string) {
            return null;
        }

        $ids = array_map('trim', explode(',', $string));

        $entities = $this->objectManager
            ->getRepository($this->class)
                ->findBy(array('id' => $ids))
        ;

        print_r($ids);

        return $entities;
    }
}
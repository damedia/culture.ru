<?php
namespace Armd\TagBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;


class TagTransformer implements DataTransformerInterface
{

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        // TODO: Implement transform() method.
    }

    /**
     * @inheritdoc
     */
    public function reverseTransform($value)
    {
        // TODO: Implement reverseTransform() method.
    }
}


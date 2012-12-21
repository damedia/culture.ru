<?php
namespace Armd\TagBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use FPN\TagBundle\Entity\TagManager;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineExtensions\Taggable\Taggable;


class TagTransformer implements DataTransformerInterface
{
    private $tagManager;

    public function __construct(TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * @throws UnexpectedTypeException   when the argument is not a string
     * @throws TransformationFailedException  when the transformation fails
     */
    public function transform($value)
    {
        $tagString = '';
        if($value instanceof ArrayCollection) {
            foreach ($value as $v) {
                $tagString .= $v->getName() . ',';
            }
            $tagString = substr($tagString, 0, -1);
        }

        return $tagString;
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws UnexpectedTypeException   when the argument is not of the expected type
     * @throws TransformationFailedException  when the transformation fails
     */
    public function reverseTransform($value)
    {
        $tags = new ArrayCollection();
        $tagNames = explode(',', $value);
        if (is_array($tagNames)) {
            foreach ($tagNames as $tagName) {
                $tags[] = $this->tagManager->loadOrCreateTag($tagName);
            }
        }
        return $tags;
    }
}
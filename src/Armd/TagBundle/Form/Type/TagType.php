<?php
namespace Armd\TagBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\OptionsResolver\Options;
use Armd\TagBundle\Form\Loader\TagLoader;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Armd\TagBundle\Form\DataTransformer\TagTransformer;


class TagType extends DoctrineType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new TagTransformer($this->om);

        $builder->prependNormTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        parent::setDefaultOptions($resolver);

        $type = $this;

        $loader = function (Options $options) use ($type) {
            return new TagLoader($options['taggable_type'], $options['em']);
        };

        $resolver->setDefaults(
            array(
                'taggable_type' => null,
                'class' => 'ArmdTagBundle:Tag',
                'loader' => $loader
            )
        );
        $resolver->setRequired(array('taggable_type'));
    }


    public function getName()
    {
        return 'armd_tag_selector';
    }

    /**
     * Return the default loader object.
     *
     * @param ObjectManager $manager
     * @param mixed         $queryBuilder
     * @param string        $class
     *
     * @throws \LogicException
     * @return EntityLoaderInterface
     */
    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {
        // this method is not used
        throw new \LogicException('TagType::getLoader must not be called');
    }
}
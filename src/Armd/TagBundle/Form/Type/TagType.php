<?php
namespace Armd\TagBundle\Form\Type;

use Armd\TagBundle\Form\EventListener\TagSubscriber;
use DoctrineExtensions\Taggable\Taggable;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\OptionsResolver\Options;
use Armd\TagBundle\Form\Loader\TagLoader;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class TagType extends DoctrineType
{
    private $tagManager;

    public function __construct(ManagerRegistry $registry, \FPN\TagBundle\Entity\TagManager $tagManager)
    {
        parent::__construct($registry);

        $this->tagManager = $tagManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new TagSubscriber($this->tagManager);
        $builder->addEventSubscriber($subscriber);

        parent::buildForm($builder, $options);
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
//                'taggable_type' => null,
                'class' => 'ArmdTagBundle:Tag',
                'loader' => $loader,
                'multiple' => true,
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
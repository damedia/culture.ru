<?php

namespace Armd\ExtendedNewsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Armd\ExtendedNewsBundle\Form\EntitiesToStringTransformer;

class TagsType extends AbstractType
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->prependClientTransformer(
            new EntitiesToStringTransformer($this->registry->getEntityManager()));
    }

    public function getParent(array $options)
    {
        // в качестве родителя подойдет простое поле
        return 'field';
    }

    public function getName()
    {
        // алиас, который используется при добавлении элементов в форму
        return 'tags';
    }
}
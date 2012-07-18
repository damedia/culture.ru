<?php

namespace Armd\AtlasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Armd\AtlasBundle\Form\CategoryType;
use Doctrine\ORM\EntityRepository;

class ObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('lon', 'number', array('precision'=>6))
            ->add('lat', 'number', array('precision'=>6))
            ->add('categories')
        ;
    }

    public function getName()
    {
        return 'armd_atlasbundle_objecttype';
    }
}
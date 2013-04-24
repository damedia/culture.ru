<?php

namespace Armd\TouristRouteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PointType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('required' => false))
            ->add('order', null, array('required' => false))
            ->add('show', null, array('required' => false))
            ->add('lat', 'hidden')
            ->add('lon', 'hidden')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Armd\TouristRouteBundle\Entity\Point',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'armd_point';
    }
}

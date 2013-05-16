<?php

namespace Armd\TouristRouteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PointCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $optionsNormalizer = function (Options $options, $value) {
            $value['block_name'] = 'entry';

            return $value;
        };

        $resolver->setDefaults(array(
            'allow_add'      => false,
            'allow_delete'   => false,
            'prototype'      => true,
            'prototype_name' => '__name__',
            'type'           => 'armd_point',
            'options'        => array(),
        ));

        $resolver->setNormalizers(array(
            'options' => $optionsNormalizer,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'armd_point_collection';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'collection';
    }
}

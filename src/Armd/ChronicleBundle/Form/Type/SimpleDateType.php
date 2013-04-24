<?php

namespace Armd\ChronicleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\FormBuilderInterface;

class SimpleDateType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->addModelTransformer(new DateTimeToStringTransformer(null, null, 'd-m-Y'));
    }

    public function getParent() {
        return 'text';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'armd_simple_date';
    }
}
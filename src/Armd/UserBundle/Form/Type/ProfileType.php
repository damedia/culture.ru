<?php

namespace Armd\UserBundle\Form\Type;

use Sonata\UserBundle\Form\Type\ProfileType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends BaseType
{
    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        parent::__construct($class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->remove('locale')
            ->remove('timezone')
            ->add('region', null, array(
                'empty_value' => '--- Выберите регион ---',
                'required' => false
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'armd_user_profile';
    }
}
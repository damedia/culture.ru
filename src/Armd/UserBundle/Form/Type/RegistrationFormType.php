<?php

namespace Armd\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder
            ->add('lastname')        
            ->add('firstname')
            ->add('middlename');
            
        parent::buildForm($builder, $options);

        $builder            
            ->add('gender')
            ->add('city')
            ->add('subscriptions', null, array('expanded' => true))
        ;
    }

    public function getName()
    {
        return 'armd_user_registration';
    }
}

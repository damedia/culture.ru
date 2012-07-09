<?php

namespace Armd\CommunicationPlatformBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProposalsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content')
            ->add('enabled')
            ->add('topic')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Armd\CommunicationPlatformBundle\Entity\Proposals'
        ));
    }

    public function getName()
    {
        return 'armd_communicationplatformbundle_proposalstype';
    }
}

<?php

namespace Armd\TvigleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TvigleType extends AbstractType
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tvigleAdmin = $this->container->get('armd_tvigle.admin.tvigle');

        $builder->add('title')
            ->add('description')
            ->add('filename', 'choice',  array('choices' => $tvigleAdmin->getVideoFileField()))
        ;



    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Armd\TvigleBundle\Entity\Tvigle',
            'translation_domain' => 'ArmdTvigleBundle'
        ));

    }

    public function getName()
    {
        return 'armd_tvigle_tvigle';
    }
}
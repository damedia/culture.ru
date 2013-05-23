<?php

namespace Armd\MediaHelperBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Armd\MediaHelperBundle\Form\EventListener\AddRemoveFieldSubscriber;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaVideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new AddRemoveFieldSubscriber($builder->getFormFactory());
        $builder->addEventSubscriber($subscriber);

        $builder
            ->add('binary_content', 'text', array(
                'label' => ' ',
                'required' => true
            ));

        if($options['with_title']) {
            $builder->add('name', null, array('required' => false));
        }

        if($options['with_description']) {
            $builder->add('description', null, array('required' => false));
        }

        $builder
            ->add('context', 'hidden', array(
                'data' => $options['media_context']
            ))
            ->add('providerName', 'hidden', array(
                'data' => $options['media_provider']
            ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['media_provider'] = $options['media_provider'];
        $view->vars['media_format'] = $options['media_format'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\MediaBundle\Entity\Media',
            'with_remove' => false,
            'with_description' => false,
            'with_title' => false,
            'media_context' => 'default',
            'media_format' => 'thumbnail',
        ))->setRequired(array('media_provider'));

    }

    public function getName()
    {
        return 'armd_media_video_type';
    }
}

<?php

namespace Armd\AtlasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MediaImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('formImageFile', 'file', array(
            'label' => ' ',
            'required' => true
        ));

        if(!empty($options['with_remove'])) {
            $builder->add('removeMedia', 'checkbox', array('label' => 'Удалить'));
        }

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\MediaBundle\Entity\Media',
            'with_remove' => false
        ));

    }

    public function getName()
    {
        return 'armd_media_image';
    }
}
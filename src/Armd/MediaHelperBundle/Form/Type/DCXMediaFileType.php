<?php

namespace Armd\MediaHelperBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Armd\MediaHelperBundle\Form\EventListener\AddRemoveFieldSubscriber;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Armd\MediaHelperBundle\Form\DataTransformer\DcxMediaTransformer;
use Doctrine\Common\Persistence\ObjectManager;

class DCXMediaFileType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new AddRemoveFieldSubscriber($builder->getFormFactory());
        $builder->addEventSubscriber($subscriber);

        $transformer = new DcxMediaTransformer($this->om);
        $builder->addModelTransformer($transformer);

        $builder
            ->add('imageFile', 'file',
            array(
                'label' => ' ',
                'required' => false
            ))
            ->add('dcxId', 'text',array(
                'label' => 'DCX Search ID',
                'required' => false
            ))
            ->add('currentMediaId', 'hidden')
            ->add('context', 'hidden', array('data' => $options['media_context']));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['media_format'] = $options['media_format'];
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'with_remove' => true,
            'media_context' => 'default',
            'media_format' => 'thumbnail',
        ));

    }

    public function getName()
    {
        return 'armd_dcx_media_file_type';
    }


}
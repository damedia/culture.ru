<?php

namespace Armd\UtilBundle\Form\Type;

use Armd\UtilBundle\Form\DataTransformers\CollectionToStringTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

use Doctrine\Common\Persistence\ObjectManager;

class ArmdEntityAjaxType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['multiple'])) {
            $builder->addViewTransformer(new CollectionToStringTransformer($this->objectManager, $options['class']));

        } elseif (empty($options['multiple']) && null !== $options['transformer']) {
            $builder->addModelTransformer($options['transformer']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $options['configs']['multiple'] = $options['multiple'];
        $view->vars['configs'] = $options['configs'];

        $view->vars['class'] = $options['class'];
        $view->vars['property'] = $options['property'];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $defaults = array(
            'placeholder'        => 'Select a value',
            'allowClear'         => false,
            'minimumInputLength' => 0,
            'width'              => 'element'
        );
        $resolver
            ->setDefaults(array(
                'configs'       => $defaults,
                'transformer'   => null,
                'multiple'      => false,
                'class'         => null,
                'property'      => null
            ))
            ->setNormalizers(array(
                'configs' => function (Options $options, $configs) use ($defaults) {
                    return array_merge($defaults, $configs);
                },
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'armd_entity_ajax';
    }
}

<?php

namespace Armd\TvigleVideoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Doctrine\Common\Persistence\ObjectManager;
use Armd\TvigleVideoBundle\Form\DataTransformer\TvigleVideoToTvigleIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TvigleVideoSelectorType extends AbstractType
{
    protected $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new TvigleVideoToTvigleIdTransformer($this->om);

        $builder->prependNormTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Tvigle ID',
            'translation_domain' => 'ArmdTvigleVideoBundle'
        ));

    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'armd_tvigle_video_selector';
    }
}
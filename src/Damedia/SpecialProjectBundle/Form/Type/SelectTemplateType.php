<?php
namespace Damedia\SpecialProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Damedia\SpecialProjectBundle\Form\EventListener\SelectTemplateFieldSubscriber;

class SelectTemplateType extends AbstractType {
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array('class' => 'DamediaSpecialProjectBundle:Template',
                                     'property' => 'title',
                                     'empty_value' => '--SELECT ONE--',
                                     'attr' => array('onchange' => 'function(){ alert(1); }')));
    }

    public function getName() {
        return 'damedia_special_project_select_template';
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $factory = $builder->getFormFactory();

        $builder->add('template', 'entity', $options);
        $builder->addEventSubscriber(new SelectTemplateFieldSubscriber($factory));
    }
}
?>
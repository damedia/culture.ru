<?php
namespace Damedia\SpecialProjectBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Damedia\SpecialProjectBundle\Form\EventListener\SelectTemplateFieldSubscriber;

class SelectTemplateType extends AbstractType {
    public function getName() {
        return 'damedia_specialProject_templateSelect';
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('template', 'entity', $options);
        $builder->addEventSubscriber(new SelectTemplateFieldSubscriber());
    }
}
?>
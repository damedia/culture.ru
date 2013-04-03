<?php
namespace Armd\TagBundle\Form\Type;

use Armd\TagBundle\Form\EventListener\TagSubscriber;
use Doctrine\ORM\EntityManager;
use FPN\TagBundle\Entity\TagManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Armd\TagBundle\Form\DataTransformer\TagTransformer;
use DoctrineExtensions\Taggable\Taggable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class TagType extends AbstractType
{
    private $tagManager;
    private $em;

    public function __construct(EntityManager $em, TagManager $tagManager)
    {
        $this->tagManager = $tagManager;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new TagSubscriber($this->tagManager))
            ->addModelTransformer(new TagTransformer($this->tagManager));

    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        if (empty($view->vars['attr']['class'])) {
            $view->vars['attr']['class'] = '';
        }
        $view->vars['attr']['class'] .= ' select2-armd-tags';
    }

    public function getName()
    {
        return 'armd_tag';
    }

    public function getParent()
    {
        return 'text';
    }

//    public function setDefaultOptions(OptionsResolverInterface $resolver) {
//        $resolver->setDefaults(
//            array(
//                'expanded' => true
//            )
//        );
//    }



}
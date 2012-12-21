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
        $data = $form->getParent()->getData();
        if ($data instanceof Taggable) {
            $tags = $this->em->getRepository('ArmdTagBundle:Tag')->findAll();
            $allTags = array();
            foreach($tags as $tag) {
                $allTags[] = $tag->getName();
            }

        } else {
            $allTags = array();
        }
        $view->vars['all_tags'] = $allTags;
    }

    public function getName()
    {
        return 'armd_tag';
    }

    public function getParent()
    {
        return 'text';
    }



}
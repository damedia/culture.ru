<?php

namespace Armd\AtlasBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ObjectCategoriesType extends AbstractType
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'armd_atlas_object_categories';
    }

    public function getParent()
    {
        return 'entity';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['root_element'] = $this->getRootElement();
        $view->vars['only_with_icon'] = $options['only_with_icon'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Armd\AtlasBundle\Entity\Category',
            'multiple' => true,
            'only_with_icon' => false
        ));
    }

    protected function getRootElement()
    {
        $root = $this->em->getRepository('Armd\AtlasBundle\Entity\Category')
            ->createQueryBuilder('c')
            ->where('c.parent  IS NULL')
            ->getQuery()
            ->getSingleResult();

        return $root;
    }
}

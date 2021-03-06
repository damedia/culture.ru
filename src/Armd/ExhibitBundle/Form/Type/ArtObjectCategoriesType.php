<?php

namespace Armd\ExhibitBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArtObjectCategoriesType extends AbstractType
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
        return 'armd_art_object_categories';
    }

    public function getParent()
    {
        return 'entity';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['root_element'] = $this->getRootElement();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Armd\ExhibitBundle\Entity\Category',
            'multiple' => true
        ));
    }

    protected function getRootElement()
    {
        $root = $this->em->getRepository('Armd\ExhibitBundle\Entity\Category')
            ->createQueryBuilder('c')
            ->where('c.parent  IS NULL')
            ->getQuery()
            ->getSingleResult();

        return $root;
    }
}

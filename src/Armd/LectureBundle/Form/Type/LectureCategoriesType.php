<?php

namespace Armd\LectureBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Armd\LectureBundle\Entity\LectureSuperType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LectureCategoriesType extends AbstractType
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
        return 'armd_lecture_categories';
    }

    public function getParent()
    {
        return 'entity';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['root_element'] = $this->getRootElement($options['super_type']);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'Armd\LectureBundle\Entity\LectureCategory',
            'multiple' => true,
        ))->setRequired(array(
            'super_type'
        ))->addAllowedTypes(array(
            'super_type' => 'Armd\LectureBundle\Entity\LectureSuperType'
        ));
    }

    protected function getRootElement(LectureSuperType $superType)
    {
        $root = $this->em->getRepository('Armd\LectureBundle\Entity\LectureCategory')
            ->createQueryBuilder('c')
            ->where('c.parent IS NULL')
            ->andWhere('c.lectureSuperType = :superType')->setParameter('superType', $superType)
            ->getQuery()
            ->getSingleResult();

        return $root;
    }
}

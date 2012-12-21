<?php

namespace Armd\LectureBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;

class LectureRolesPersonsType extends AbstractType
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
        return 'armd_lecture_roles_persons';
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Armd\LectureBundle\Entity\LectureRolePerson',
        );
    }

}

<?php

namespace Armd\BlogBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Doctrine\ORM\Query\Expr\Join;

/**
 * User group type
 *
 * @todo: add options for not load root and disabled elements
 */
class BloggerType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'class' => 'Armd\UserBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    $qb = $er->createQueryBuilder('u')
                        ->leftJoin('u.groups', 'g');
                    $qb->where("g.roles LIKE :role")
                        ->setParameter('role', '%ROLE_BLOGGER%');

                    return $qb;
                }
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'blogger_type';
    }
}
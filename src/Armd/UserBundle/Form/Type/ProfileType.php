<?php

namespace Armd\UserBundle\Form\Type;

use Sonata\UserBundle\Form\Type\ProfileType as BaseType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends BaseType
{
    /**
     * @param string $class The User class name
     */
    public function __construct($class)
    {
        parent::__construct($class);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->remove('locale')
            ->remove('timezone')
            ->remove('biography')
            ->add('region', null, array(
                'empty_value' => '--- Выберите регион ---',
                'required' => false,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.sortIndex', 'ASC')
                        ->addOrderBy('r.title', 'ASC');
                }
            ))
            ->add('biographyText')
            ->add('vkontakteUid')
            ->add('facebookName')
            ->add('twitterName')
            ->add('subscriptions', null, array('expanded' => true))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'armd_user_profile';
    }
}

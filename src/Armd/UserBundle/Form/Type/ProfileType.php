<?php

namespace Armd\UserBundle\Form\Type;

use Sonata\UserBundle\Form\Type\ProfileType as BaseType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Armd\MkCommentBundle\Entity\Notice;

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
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add('middlename')
            ->add('dateOfBirth', 'birthday', array('required' => false))
            ->add('region', null, array(
                'empty_value' => '--- Выберите регион ---',
                'required' => false,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->orderBy('r.sortIndex', 'ASC')
                        ->addOrderBy('r.title', 'ASC');
                }
            ))
            ->add('phone', null, array('required' => false))
            ->add('website', null, array('required' => false))
            ->add('biographyText', null, array('label' => 'Biography'))
            ->add('vkontakteUid')
            ->add('facebookName')
            ->add('twitterName')
            ->add('subscriptions', null, array('expanded' => true))
            ->add('noticeOnComment', 'choice', array('choices' => array(
                    Notice::T_NONE => 'NOTICE_T_NONE',
                    Notice::T_REPLY => 'NOTICE_T_REPLY',
                    Notice::T_THREAD => 'NOTICE_T_THREAD',
                    Notice::T_ALL => 'NOTICE_T_ALL',
                )))
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

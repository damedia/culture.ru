<?php

namespace Armd\UserBundle\Form\Type;

use Sonata\UserBundle\Form\Type\ProfileType as BaseType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Armd\MkCommentBundle\Entity\Notice;
use Symfony\Component\Security\Core\Validator\Constraint\UserPassword;

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
//            ->add('biographyText', null, array('label' => 'Biography'))
            ->add('vkontakteUid')
            ->add('facebookName')
            ->add('twitterName')
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            
            ->add('current_password', 'password', array(
                    'label' => 'form.current_password',
                    'translation_domain' => 'FOSUserBundle',
                    'mapped' => false,
                    'constraints' => new UserPassword(),
                ))
                
            ->add('plainPassword', 'repeated', array(
                        'required' => false,
                        'type' => 'password',
                        'options' => array('translation_domain' => 'FOSUserBundle'),
                        'first_options' => array('label' => 'form.new_password'),
                        'second_options' => array('label' => 'form.new_password_confirmation'),
                        'invalid_message' => 'fos_user.password.mismatch',
                    ))
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

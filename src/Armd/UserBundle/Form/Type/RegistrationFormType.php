<?php

namespace Armd\UserBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    protected $em;
    /**
     * @param string $class The User class name
     */
    public function __construct($class, ObjectManager $em)
    {
        parent::__construct($class);
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // add your custom field
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add('middlename');

        parent::buildForm($builder, $options);

        $builder
            ->add('gender')
            ->add('city')
            ->add('subscriptions', null, array('expanded' => true, 'data' => $this->getSubscriptions()))
        ;
    }

    public function getSubscriptions()
    {
        $subscriptions = $this->em->getRepository('ArmdSubscriptionBundle:MailingList')
            ->findBy(array('enabled' => true));

        return new ArrayCollection($subscriptions);
    }

    public function getName()
    {
        return 'armd_user_registration';
    }
}

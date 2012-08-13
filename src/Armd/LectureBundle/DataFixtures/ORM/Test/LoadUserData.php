<?php
namespace Armd\AtlasBundle\DataFixtures\ORM\Test;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Armd\UserBundle\Entity\User;
use Armd\UserBundle\Entity\Group;
use Armd\UserBundle\Entity\UserManager;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager.default');

        $group = new Group('Супер администраторы', array('ROLE_SUPER_ADMIN'));
        $manager->persist($group);
        $manager->flush();

        $user = new User();
        $user->setGroups(array($group));
        $user->setEmail('test@example.org');
        $user->setPlainPassword('111111');
        $user->setUsername('superadmin');
        $user->setEnabled(true);
        $userManager->updateUser($user, true);

    }

}

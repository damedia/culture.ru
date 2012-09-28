<?php
namespace Armd\AtlasBundle\DataFixtures\ORM\Test;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Armd\UserBundle\Entity\User;
use Armd\UserBundle\Entity\Group;
use Armd\UserBundle\Entity\UserManager;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userCount = $manager->getRepository('ArmdUserBundle:User')
            ->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->getQuery()->getSingleScalarResult();

        if($userCount == 0) {
            $this->loadAdmins($manager);
            $this->loadUsers($manager);
        }


    }

    public function loadAdmins($manager)
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
        $user->setSalt('123');
        $user->setLocked(false);
        $user->setExpired(false);
        $user->setCredentialsExpired(false);
        $user->setRoles(array('ROLE_ADMIN', 'ROLE_SONATA_ADMIN'));
        $userManager->updateUser($user, true);

    }

    public function loadUsers($manager)
    {
        $userManager = $this->container->get('fos_user.user_manager.default');

        $group = new Group('Эксперты', array('ROLE_EXPERT'));
        $manager->persist($group);
        $manager->flush();

        $user = new User();
        $user->setGroups(array($group));
        $user->setEmail('test1@example.org');
        $user->setPlainPassword('111111');
        $user->setUsername('expert1');
        $user->setEnabled(true);
        $user->setSalt('123');
        $user->setLocked(false);
        $user->setExpired(false);
        $user->setCredentialsExpired(false);
//        $user->setRoles(array('ROLE_ADMIN', 'ROLE_SONATA_ADMIN'));
        $userManager->updateUser($user, true);
        $this->addReference('armd_user.user.expert1', $user);

        $user = new User();
        $user->setGroups(array($group));
        $user->setEmail('test2@example.org');
        $user->setPlainPassword('111111');
        $user->setUsername('expert2');
        $user->setEnabled(true);
        $user->setSalt('123');
        $user->setLocked(false);
        $user->setExpired(false);
        $user->setCredentialsExpired(false);
//        $user->setRoles(array('ROLE_ADMIN', 'ROLE_SONATA_ADMIN'));
        $userManager->updateUser($user, true);

        $user = new User();
        $user->setGroups(array($group));
        $user->setEmail('test3@example.org');
        $user->setPlainPassword('111111');
        $user->setUsername('expert3');
        $user->setEnabled(true);
        $user->setSalt('123');
        $user->setLocked(false);
        $user->setExpired(false);
        $user->setCredentialsExpired(false);
//        $user->setRoles(array('ROLE_ADMIN', 'ROLE_SONATA_ADMIN'));
        $userManager->updateUser($user, true);

    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 10;
    }
}

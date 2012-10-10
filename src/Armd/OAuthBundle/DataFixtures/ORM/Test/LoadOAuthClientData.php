<?php
namespace Armd\OAuthBundle\DataFixtures\ORM\Test;

use Doctrine\Common\DataFixtures\AbstractFixture;
use OAuth2\OAuth2;
use Doctrine\Common\Persistence\ObjectManager;
use Armd\OAuthBundle\Entity\Client;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadOAuthClientData extends AbstractFixture implements OrderedFixtureInterface {

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    function load(ObjectManager $manager)
    {

        $client = new Client();
        $client->setRandomId('client1');
        $client->setSecret('client1_secret123');
        $client->setAllowedGrantTypes(array(OAuth2::GRANT_TYPE_USER_CREDENTIALS));
        $manager->persist($client);

        $client = new Client();
        $client->setRandomId('client2');
        $client->setSecret('client2_secret123');
        $client->setAllowedGrantTypes(array(OAuth2::GRANT_TYPE_USER_CREDENTIALS));
        $manager->persist($client);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 100;
    }
}
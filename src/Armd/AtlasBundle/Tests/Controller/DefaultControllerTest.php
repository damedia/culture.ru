<?php

namespace Armd\AtlasBundle\Tests\Controller;

use Armd\AtlasBundle\Tests\AbstractFunctionalTest;

class DefaultControllerTest extends AbstractFunctionalTest
{


    public function testObjectViewAction()
    {
        $this->loadFixtures();

        $object = $this->em->getRepository('ArmdAtlasBundle:Object')->findOneBy(array());

        $client = static::createClient();
        $client->request('GET', $this->router->generate('armd_atlas_default_object_view', array('id' => $object->getId())));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}

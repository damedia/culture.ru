<?php
namespace Armd\TagBundle\DataFixtures\ORM\Test;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Yaml\Parser;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadTestObjectData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @return void
     */
    function load(ObjectManager $manager)
    {
        $parser = new Parser();

        /** @var \FPN\TagBundle\Entity\TagManager $tagManager  */
        $tagManager = $this->container->get('fpn_tag.tag_manager');

        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_tags.yml'));

        foreach ($data['tags'] as $tagData) {
            $tag = $tagManager->loadOrCreateTag($tagData['tag']);
            $object = $this->getReference($tagData['object']);

            $tagManager->loadTagging($object);
            $tagManager->addTag($tag, $object);
            $tagManager->saveTagging($object);
        }
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 500;
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
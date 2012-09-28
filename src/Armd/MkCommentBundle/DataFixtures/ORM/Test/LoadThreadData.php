<?php
namespace Armd\MkCommentBundle\DataFixtures\ORM\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\Yaml\Parser;
use Armd\MkCommentBundle\Entity\Thread;
use Armd\AtlasBundle\Entity\Object as AtlasObject;

class LoadThreadData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    protected $container;
    protected $router;
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @return void
     */
    function load(ObjectManager $manager)
    {
        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_threads.yml'));
        foreach($data['threads'] as $threadData) {
            $thread = $this->createThread($threadData);
            $manager->persist($thread);
        }
        $manager->flush();

    }

    function createThread($threadData)
    {
        $thread = new Thread();

        if (!empty($threadData['object'])) {
            $object = $this->getReference($threadData['object']);

            $thread->setId($this->getObjectThreadId($object));
            $thread->setPermalink($this->getObjectPermalink($object));
        } else {
            throw new \Exception('thread object reference must be given');
        }

        if(!empty($threadData['ref_code'])) {
            $this->addReference($threadData['ref_code'], $thread);
        }

        return $thread;
    }

    function getObjectThreadId($object)
    {
        if($object instanceof AtlasObject) {
            $threadId = 'atlas_' . $object->getId();
        } else {
            throw new \Exception('Unknown comment thread object type');
        }

        return $threadId;
    }

    function getObjectPermalink($object)
    {
        if($object instanceof AtlasObject) {
            $permalink = $this->router->generate('armd_atlas_default_object_view', array('id' => $object->getId()), true);
        } else {
            throw new \Exception('Unknown comment thread object type');
        }

        return $permalink;

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
        $this->router = $container->get('router');
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 420;
    }

}
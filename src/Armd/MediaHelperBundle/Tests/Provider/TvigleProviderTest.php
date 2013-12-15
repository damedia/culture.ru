<?php
namespace Armd\MediaHelperBundle\Tests\Provider;

require_once dirname(__DIR__).'/../../../../app/AppKernel.php';

class TvigleProviderTest extends \PHPUnit_Framework_TestCase {
    protected $kernel;
    protected $container;
    protected $entityManager;
    protected $tvigleProvider;

    public function setUp() {
        $this->kernel = new \AppKernel('test', true);
        $this->kernel->boot();

        $this->container = $this->kernel->getContainer();
        $this->entityManager = $this->container->get('doctrine')->getManager();
        $this->tvigleProvider = $this->container->get('sonata.media.provider.tvigle');

        parent::setUp();
    }

    public function tearDown() {
        $this->kernel->shutdown();

        parent::tearDown();
    }

    public function testGetMetadata_noBinaryContent() {
        $mediaId_noBinaryContent = 28259;

        $mediaRepository = $this->entityManager->getRepository('Application\Sonata\MediaBundle\Entity\Media');
        $mediaEntity_noBinaryContent = $mediaRepository->find($mediaId_noBinaryContent);

        if (!is_object($mediaEntity_noBinaryContent)) {
            $this->fail('Test Media entity with id = '.$mediaId_noBinaryContent.' and name = "'.$mediaEntity_noBinaryContent->getName().'" does not exist!');
        }

        if ($mediaEntity_noBinaryContent->getBinaryContent() !== null) {
            $this->fail('Test Media entity with id = '.$mediaId_noBinaryContent.' has non empty "binaryContent" while it should!');
        }

        $metadata = $this->tvigleProvider->getMetadata($mediaEntity_noBinaryContent);

        $this->assertNull($metadata);
    }

    public function testAllMediaEntitiesForHavingNoBinaryContent() {
        //$this->markTestSkipped('This test is skipped for speed!');

        $mediaRepository = $this->entityManager->getRepository('Application\Sonata\MediaBundle\Entity\Media');
        $mediaEntities = $mediaRepository->findAll();

        foreach ($mediaEntities as $entity) {
            if ($entity->getBinaryContent() !== null) {
                $this->fail('Test Media entity with id = '.$entity->getId().' has binaryContent while it should not!');
            }

            $metadata = $this->tvigleProvider->getMetadata($entity);
            $this->assertNull($metadata);
        }
    }

    public function testGetMetadata_withBinaryContent() {
        $this->markTestIncomplete('We have no Media entities with binaryContent to test and I think that we should not have any!');
    }
}
?>
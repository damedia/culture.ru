<?php
namespace Armd\MkCommentBundle\DataFixtures\ORM\Test;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use FOS\CommentBundle\Event\CommentPersistEvent;
use FOS\CommentBundle\Events;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use FOS\CommentBundle\Model\CommentInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\Yaml\Parser;
use Armd\MkCommentBundle\Entity\Comment;
use Armd\MkCommentBundle\Entity\Thread;
use Armd\AtlasBundle\Entity\Object as AtlasObject;
use FOS\CommentBundle\Event\CommentEvent;


class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    protected $manager;
    protected $container;
    protected $router;
    protected $dispatcher;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     * @return void
     */
    function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../Resources/fixtures/test_comments.yml'));
        foreach($data['comments'] as $threadData) {
            $thread = $this->createThread($threadData);
            $manager->persist($thread);
        }
        $manager->flush();
    }

    public function createThread($threadData)
    {
        $thread = new Thread();
        $thread->setId($this->getObjectThreadId($this->getReference($threadData['object'])));
        $thread->setPermalink($this->getObjectPermalink($this->getReference($threadData['object'])));
        foreach ($threadData['comments'] as $commentData) {
            $this->createComment($thread, null, $commentData);
        }

        return $thread;

    }

    public function createComment($thread, $parentComment, $commentData)
    {
        $commentManager = $this->container->get('fos_comment.manager.comment');
        $comment = $commentManager->createComment($thread, $parentComment);
        $comment->setAuthor($this->getReference($commentData['author']));
        $comment->setCreatedAt(new \DateTime($commentData['createdAt']));
        $comment->setState(constant('\FOS\CommentBundle\Model\CommentInterface::' . $commentData['state']));
        if(!empty($commentData['moderatedBy'])) {
            $comment->setModeratedBy($this->getReference($commentData['moderatedBy']));
        }
        if(!empty($commentData['moderatedAt'])) {
            $comment->setModeratedAt(new \DateTime($commentData['moderatedAt']));
        }
        $comment->setBody($commentData['body']);
        $comment->setSkipAutoModerate(true);

        //--- save comment
        $event = new CommentPersistEvent($comment);
        $this->dispatcher->dispatch(Events::COMMENT_PRE_PERSIST, $event);

        $this->manager->persist($thread);
        $this->manager->persist($comment);

        $event = new CommentEvent($comment);
        $this->dispatcher->dispatch(Events::COMMENT_POST_PERSIST, $event);
//        $commentManager->saveComment($comment);
        //--- /save comment

        // child comments
        if (!empty($commentData['comments'])) {
            foreach($commentData['comments'] as $subCommentData) {
                $this->createComment($thread, $comment, $subCommentData);
            }
        }

        return $comment;
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
        $this->dispatcher = $container->get('event_dispatcher');
    }


    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    function getOrder()
    {
        return 430;
    }

}
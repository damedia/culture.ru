<?php

namespace Armd\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Armd\ContentAbstractBundle\Entity\Stream;

class LoadStreamData extends AbstractFixture implements OrderedFixtureInterface
{
    protected $em;
    
    public function load(ObjectManager $manager)
    {
        $streams = array(
            'main'  => 'Новости: Главная страница',
            'news'  => 'Новости: Пресс-Центр',
        );
        
        $this->em = $manager;
        
        foreach($streams as $page => $name) {
            $stream = $this->createStream($name);
            $this->addReference("stream-news-{$page}", $stream);
        }
        
        $this->em->flush();
    }
    
    public function createStream($name)
    {
        $stream = new Stream();
        $stream->setName($name);
    
        $this->em->persist($stream);
        
        return $stream;
    }
    
    public function getOrder()
    {
        return 10;
    }
}
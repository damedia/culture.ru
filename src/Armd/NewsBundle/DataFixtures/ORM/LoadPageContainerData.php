<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Armd\Bundle\CmsBundle\Entity\PageContainer;

class LoadPageContainerData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        foreach ( $this->getMenuData() as $element ) {
            $pageContainer = new PageContainer();

            if ( $this->hasReference($element['рage']) ) {
                $pageContainer->setPage($this->getReference($element['рage']));
            }

            if ( $this->hasReference($element['сontainer']) ) {
                $pageContainer->setContainer($this->getReference($element['сontainer']));
            }

            $pageContainer->setUsageService($element['usageService']);
            $pageContainer->setUsageType($element['usageType']);

            if ( isset($element['content_stream']) && $this->hasReference($element['content_stream']) ) {
                $pageContainer->setSettings(array($element['usageService'] => array('content_stream' => $this->getReference($element['content_stream'])->getId())));
            }

            $this->addReference('pageContainer-' . $element['name'], $pageContainer);

            $manager->persist($pageContainer);
        }

        $manager->flush();
    }

    public function getMenuData()
    {
        return array(array('рage' => 'page-main',
                           'сontainer' => 'container-content',
                           'usageService' => 'armd_news',
                           'usageType' => 'list',
                           'content_stream' => 'stream-news-main',
                           'name' => 'main'),
                     array('рage' => 'page-news',
                           'сontainer' => 'container-content',
                           'usageService' => 'armd_news',
                           'usageType' => 'listitem',
                           'content_stream' => 'stream-news-main',
                           'name' => 'news'),
                     array('рage' => 'page-anounce',
                           'сontainer' => 'container-content',
                           'usageService' => 'armd_news',
                           'usageType' => 'listitem',
                           'content_stream' => 'stream-news-news',
                           'name' => 'anounce'));
    }

    public function getOrder()
    {
        return 102;
    }

}
